<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\AdvisoryController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\Admin\ProcurementController;
use App\Http\Controllers\Frontend\BidOpportunityController;
use App\Http\Controllers\Frontend\FileAccessController;
use App\Models\Banner;
use App\Models\Advisory; 
use App\Models\Faq; 

// Curriculum Controllers
use App\Http\Controllers\Frontend\CurriculumController as FrontendCurriculumController;
use App\Http\Controllers\Admin\CurriculumController as AdminCurriculumController;
use App\Http\Controllers\Admin\FaqController; 

// Learning Materials Controllers
use App\Http\Controllers\Frontend\LearningMaterialsController as FrontendLearningMaterialsController;
use App\Http\Controllers\Admin\LearningMaterialsController as AdminLearningMaterialsController;

// Modules Controllers
use App\Http\Controllers\Frontend\ModulesController as FrontendModulesController;
use App\Http\Controllers\Admin\ModulesController as AdminModulesController;

// Site Settings Controller
use App\Http\Controllers\Admin\SiteSettingController;

// QMS, Vision Mission, Data Privacy, and Citizen Charter Controllers
use App\Http\Controllers\Admin\QmsController;
use App\Http\Controllers\Admin\VisionMissionController;
use App\Http\Controllers\Admin\DataPrivacyController;
use App\Http\Controllers\Admin\CitizenCharterController;
use App\Models\Qms;
use App\Models\VisionMission;
use App\Models\DataPrivacy;
use App\Models\CitizenCharter;

// ==========================================
// PUBLIC ROUTES
// ==========================================

Route::get('/serve-image/{path}', function($path) {
    $absolutePath = storage_path('app/public/' . $path);
    if (!file_exists($absolutePath)) {
        abort(404, 'Image not found on disk.');
    }
    return response()->file($absolutePath);
})->where('path', '.*')->name('serve.image');


Route::get('/', function () {
    $latestAdvisory = Advisory::latest()->first(); 
    $dbBanners = Banner::all();
    
    if($dbBanners->isEmpty()) {
        $banners = collect([
            asset('images/r9.png'), 
            asset('images/foi.png'), 
            asset('images/deped.png')
        ]);
    } else {
        $banners = $dbBanners->map(fn($banner) => asset('storage/' . $banner->image_path));
    }

    return view('index', compact('latestAdvisory', 'banners'));
})->name('login'); 

// Admin Login
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

// Quality Management System (QMS) Public Route
Route::get('/qms', function () {
    $qms = Qms::first();
    return view('qms.index', compact('qms'));
})->name('qms.index');

// Vision, Mission, Core Values, Mandate Public Route
Route::get('/vision-mission', function () {
    $data = VisionMission::first();
    return view('vision_mission.index', compact('data'));
})->name('vision_mission.index');

// Data Privacy Notice Public Route
Route::get('/data-privacy', function () {
    $data = DataPrivacy::first();
    return view('data_privacy.index', compact('data'));
})->name('data_privacy.index');

// Citizen's Charter Public Route
Route::get('/citizens-charter', function () {
    $data = CitizenCharter::first();
    return view('citizen_charter.index', compact('data'));
})->name('citizen_charter.index');

// Public Issuances
Route::get('/issuances/advisories', [IssuanceController::class, 'advisories'])->name('issuances.advisories');
Route::get('/issuances/memoranda', [IssuanceController::class, 'memoranda'])->name('issuances.memoranda');
Route::get('/issuances/hrmpsb', [IssuanceController::class, 'hrmpsb'])->name('issuances.hrmpsb');
Route::get('/issuances/view/{issuance}', [IssuanceController::class, 'show'])->name('issuances.show');

// Frontend Learning Materials Routes
Route::get('/k-to-12/learning-materials', [FrontendLearningMaterialsController::class, 'index'])->name('learning_materials.index');
Route::get('/k-to-12/learning-materials/{id}', [FrontendLearningMaterialsController::class, 'show'])->name('learning_materials.show');

// K to 12 Nested Routes
Route::prefix('k-to-12')->name('k12.')->group(function () {
    
    // About under K to 12
    Route::prefix('about')->name('about.')->group(function () {
        Route::get('/curriculum', [FrontendCurriculumController::class, 'index'])->name('curriculum');
        Route::get('/faq', function () {
            $faqs = Faq::where('is_active', true)->get();
            return view('curriculum.faq', compact('faqs'));
        })->name('faq');
    });

    // Alternative Learning System (ALS)
    Route::prefix('als')->name('als.')->group(function () {
        Route::get('/about', [IssuanceController::class, 'alsContent'])->name('about');
        Route::get('/statistics', [IssuanceController::class, 'alsContent'])->name('stats');
        Route::get('/stories', [IssuanceController::class, 'alsContent'])->name('stories');
        
        // Modules Controller
        Route::get('/modules', [FrontendModulesController::class, 'index'])->name('modules');
        Route::get('/modules/{id}', [FrontendModulesController::class, 'show'])->name('modules.show');
        
        Route::get('/implementer-of-the-month', [IssuanceController::class, 'alsContent'])->name('implementer');
    });

    Route::get('/junior-high', [IssuanceController::class, 'k12Content'])->name('junior-high');
    Route::get('/senior-high', [IssuanceController::class, 'k12Content'])->name('senior-high');
});

// Public Procurement
Route::prefix('procurement/{category}')->name('procurement.')->group(function () {
    Route::get('/', [BidOpportunityController::class, 'index'])->name('index');
    Route::get('/{id}', [BidOpportunityController::class, 'show'])->name('show');
});

// ==========================================
// SECURE ROUTES
// ==========================================

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('/procurement/file/{id}/{type}', [FileAccessController::class, 'show'])->name('procurement.file.access');

    // Protected Admin Management
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard
        Route::middleware(['permission:dashboard'])->group(function () {
            Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        });

        // Site Settings & "Manage About" Content
        Route::middleware(['permission:settings'])->group(function () {
            Route::get('/settings', [SiteSettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
            
            // Dedicated QMS Routes
            Route::get('/qms', [QmsController::class, 'index'])->name('qms.index');
            Route::post('/qms', [QmsController::class, 'update'])->name('qms.update');

            // Dedicated Vision & Mission Routes
            Route::get('/vision-mission', [VisionMissionController::class, 'index'])->name('vision_mission.index');
            Route::post('/vision-mission', [VisionMissionController::class, 'update'])->name('vision_mission.update');

            // Dedicated Data Privacy Route
            Route::get('/data-privacy', [DataPrivacyController::class, 'index'])->name('data_privacy.index');
            Route::post('/data-privacy', [DataPrivacyController::class, 'update'])->name('data_privacy.update');

            // Dedicated Citizen's Charter Routes
            Route::get('/citizens-charter', [CitizenCharterController::class, 'index'])->name('citizen_charter.index');
            Route::post('/citizens-charter', [CitizenCharterController::class, 'update'])->name('citizen_charter.update');
        });

        // Site Logos
        Route::middleware(['permission:logos'])->group(function () {
            Route::resource('logos', \App\Http\Controllers\Admin\SiteLogoController::class)->except(['create', 'show', 'edit']);
        });

        // FAQ Management
        Route::middleware(['permission:faq'])->group(function () {
            Route::resource('faq', FaqController::class)->except(['create', 'show', 'edit']);
        });

        // Learning Materials
        Route::middleware(['permission:materials'])->group(function () {
            Route::resource('learning-materials', AdminLearningMaterialsController::class);
            Route::get('get-learning-materials-data', [AdminLearningMaterialsController::class, 'getData'])->name('learning_materials.data');
        });

        // ALS Modules
        Route::middleware(['permission:modules'])->group(function () {
            Route::resource('modules', AdminModulesController::class);
            Route::get('get-modules-data', [AdminModulesController::class, 'getData'])->name('modules.data');
        });

        // Curriculum Management
        Route::middleware(['permission:curriculum'])->prefix('curriculum')->name('curriculum.')->group(function () {
            Route::get('/', [AdminCurriculumController::class, 'index'])->name('index');
            Route::post('/page', [AdminCurriculumController::class, 'updatePage'])->name('update_page');
            Route::post('/strands', [AdminCurriculumController::class, 'storeStrand'])->name('strands.store');
            Route::put('/strands/{strand}', [AdminCurriculumController::class, 'updateStrand'])->name('strands.update');
            Route::delete('/strands/{strand}', [AdminCurriculumController::class, 'destroyStrand'])->name('strands.destroy');
            Route::post('/materials', [AdminCurriculumController::class, 'storeMaterial'])->name('materials.store');
            Route::delete('/materials/{material}', [AdminCurriculumController::class, 'destroyMaterial'])->name('materials.destroy');
            Route::post('/guides', [AdminCurriculumController::class, 'storeGuide'])->name('guides.store');
            Route::put('/guides/{guide}', [AdminCurriculumController::class, 'updateGuide'])->name('guides.update');
            Route::delete('/guides/{guide}', [AdminCurriculumController::class, 'destroyGuide'])->name('guides.destroy');
        });

        // User Management
        Route::middleware(['permission:users'])->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update'); 
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Advisories
        Route::middleware(['permission:advisories'])->group(function () {
            Route::get('/advisories', [AdvisoryController::class, 'index'])->name('advisory.index');
            Route::post('/advisories/store', [AdvisoryController::class, 'store'])->name('advisories.store');
            Route::put('/advisories/{advisory}', [AdvisoryController::class, 'update'])->name('advisories.update');
            Route::delete('/advisories/{advisory}', [AdvisoryController::class, 'destroy'])->name('advisories.destroy');
        });

        // Banners
        Route::middleware(['permission:banners'])->group(function () {
            Route::get('/banners', [BannerController::class, 'adminIndex'])->name('banners.index');
            Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
            Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
        });

        // Issuances (Strictly checks the requested type based on specific checklist permissions)
        Route::prefix('issuances')->name('issuances.')->group(function () {
            
            // 1. Intercept the view request and check the specific tab permission
            Route::get('/', function (\Illuminate\Http\Request $request) {
                $type = $request->query('type', 'advisory'); // default to advisory if empty
                
                $permissionRequired = match($type) {
                    'memorandum' => 'memoranda',
                    'hrmpsb'     => 'hrmpsb',
                    default      => 'advisories'
                };
                
                if (!auth()->user()->hasPermission($permissionRequired)) {
                    abort(403, 'Unauthorized Access. You do not have permission to view ' . strtoupper($type) . 'S.');
                }
                
                return app(\App\Http\Controllers\IssuanceController::class)->adminIndex($request);
            })->name('index');

            // 2. Allow modifying records as long as they have at least one issuance permission
            Route::middleware(['permission:memoranda,hrmpsb,advisories'])->group(function() {
                Route::post('/', [IssuanceController::class, 'store'])->name('store');
                Route::put('/{issuance}', [IssuanceController::class, 'update'])->name('update');
                Route::delete('/{issuance}', [IssuanceController::class, 'destroy'])->name('destroy');
            });
        });

        // Procurement Management
        Route::middleware(['permission:procurement'])->prefix('procurement/{category}')->name('procurement.')->group(function () {
            Route::get('/', [ProcurementController::class, 'index'])->name('index');
            Route::post('/', [ProcurementController::class, 'store'])->name('store');
            Route::put('/{id}', [ProcurementController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProcurementController::class, 'destroy'])->name('destroy');
        });
    }); 
});