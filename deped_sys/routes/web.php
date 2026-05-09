<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\AdvisoryController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\Admin\RoleController; 
use App\Http\Controllers\Admin\ProfileController; 
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

// --- ELEMENTARY, JUNIOR & SENIOR HIGH CONTROLLERS (UPDATED) ---
use App\Http\Controllers\Admin\ElementaryController;
use App\Http\Controllers\Admin\JuniorHighController;
use App\Http\Controllers\Admin\SeniorHighController;
use App\Http\Controllers\Frontend\ElementaryFrontendController;
use App\Http\Controllers\Frontend\JuniorHighFrontendController;
use App\Http\Controllers\Frontend\SeniorHighFrontendController;

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

// --- IMPORTS FOR DYNAMIC PAGES ---
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;
use App\Http\Controllers\Admin\PageSectionController; // <-- ADDED: Page Section Controller

// --- IMPORTS FOR ORGANIZATIONAL CHART & DIVISIONS ---
use App\Http\Controllers\OrgChartController;
use App\Http\Controllers\Admin\OrgChartAdminController;
use App\Http\Controllers\Admin\DivisionStructureController;
use App\Http\Controllers\Frontend\DivisionStructureController as FrontendDivisionStructureController;

// --- ENROLLMENT STATISTICS ---
use App\Http\Controllers\Admin\EnrollmentStatisticController;
use App\Http\Controllers\Frontend\EnrollmentStatisticController as FrontendEnrollmentStatisticController;

// --- ALS STORIES ---
use App\Http\Controllers\Admin\AlsStoryController as AdminAlsStoryController;
use App\Http\Controllers\Frontend\AlsStoryController as FrontendAlsStoryController;

// --- ALS IMPLEMENTERS ---
use App\Http\Controllers\Admin\AlsImplementerController as AdminAlsImplementerController;
use App\Http\Controllers\Frontend\AlsImplementerController as FrontendAlsImplementerController;

// --- SGOD ---
use App\Http\Controllers\Admin\SgodController as AdminSgodController;
use App\Http\Controllers\Frontend\SgodController as FrontendSgodController;

// --- OSDS ---
use App\Http\Controllers\Admin\OsdsController as AdminOsdsController;
use App\Http\Controllers\Frontend\OsdsController as FrontendOsdsController;

// --- CID ---
use App\Http\Controllers\Admin\CidController as AdminCidController;
use App\Http\Controllers\Frontend\CidController as FrontendCidController;

// ==========================================
// PUBLIC ROUTES
// ==========================================

// --- BULLETPROOF CKEDITOR UPLOAD ROUTE ---
Route::post('/editor/upload-image', [AdminPageController::class, 'uploadImage'])->name('editor.upload');

// --- BACKEND VIDEO SHAPE API ---
Route::get('/api/video-shape', [AdminPageController::class, 'checkVideoShape'])->name('api.video.shape');


Route::get('/serve-image/{path}', function($path) {
    $absolutePath = storage_path('app/public/' . $path);
    if (!file_exists($absolutePath)) {
        abort(404, 'Image not found on disk.');
    }
    return response()->file($absolutePath);
})->where('path', '.*')->name('serve.image');


Route::get('/', function () {
    $latestAdvisory = Advisory::latest()->first(); 
    
    // UPDATED: Only get active banners and sort them by the sort_order column
    $dbBanners = Banner::where('is_active', true)->orderBy('sort_order', 'asc')->get();
    
    if($dbBanners->isEmpty()) {
        $banners = collect([
            asset('images/r9.png'), 
            asset('images/foi.png'), 
            asset('images/deped.png')
        ]);
    } else {
        $banners = $dbBanners->map(fn($banner) => asset('storage/' . $banner->image_path));
    }

    return view('/home/index', compact('latestAdvisory', 'banners'));
}); 


// ==========================================
// ADMIN AUTHENTICATION ROUTES
// ==========================================

// 1. Show the dedicated login page (Given the 'login' name for Laravel's auth middleware)
Route::get('/admin/login', function () {
    // Note: Make sure your blade file is named login.blade.php and is inside the resources/views/auth folder
    // If you saved it in resources/views/admin/login.blade.php, change 'auth.login' to 'admin.login'
    return view('auth.login'); 
})->name('login');

// 2. Handle the login POST request
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

// 3. Admin Password Reset Routes
Route::post('/admin/password/email', [AdminController::class, 'sendResetCode'])->name('admin.password.email');
Route::post('/admin/password/reset', [AdminController::class, 'resetPassword'])->name('admin.password.reset');

// 4. Fallbacks: If someone types the reset routes in the URL bar, redirect to the new login page
Route::get('/admin/password/email', function () { return redirect()->route('login'); });
Route::get('/admin/password/reset', function () { return redirect()->route('login'); });


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

// Organizational Structure - Executive Committee (Dynamic Chart)
Route::get('/about/organizational-structure/executive-committee', [OrgChartController::class, 'index'])->name('org.chart');

// Organizational Structure - Division Offices (Dynamic Page)
Route::get('/about/organizational-structure/division-offices', [FrontendDivisionStructureController::class, 'index'])->name('division_offices.index');

// Organizational Structure - SGOD
Route::get('/about/organizational-structure/sgod', [FrontendSgodController::class, 'index'])->name('sgod.index');

// Organizational Structure - OSDS
Route::get('/about/organizational-structure/osds', [FrontendOsdsController::class, 'index'])->name('osds.index');

// Organizational Structure - CID
Route::get('/about/organizational-structure/cid', [FrontendCidController::class, 'index'])->name('cid.index');

// Public Issuances
Route::get('/issuances', [IssuanceController::class, 'index'])->name('issuances.public'); // <-- ADDED THIS LINE
Route::get('/issuances/advisories', [IssuanceController::class, 'advisories'])->name('issuances.advisories');
Route::get('/issuances/memoranda', [IssuanceController::class, 'memoranda'])->name('issuances.memoranda');
Route::get('/issuances/hrmpsb', [IssuanceController::class, 'hrmpsb'])->name('issuances.hrmpsb');
Route::get('/issuances/view/{issuance}', [IssuanceController::class, 'show'])->name('issuances.show');

// GLOBAL SEARCH ROUTE
Route::get('/search', [IssuanceController::class, 'globalSearch'])->name('search.global');

// Frontend Learning Materials Routes
Route::get('/k-to-12/learning-materials', [FrontendLearningMaterialsController::class, 'index'])->name('learning_materials.index');
Route::get('/k-to-12/learning-materials/{id}', [FrontendLearningMaterialsController::class, 'show'])->name('learning_materials.show');

// Public Enrollment Statistics Routes
Route::get('/enrollment-statistics', [FrontendEnrollmentStatisticController::class, 'index'])->name('enrollment-statistics.index');
Route::get('/enrollment-statistics/{id}', [FrontendEnrollmentStatisticController::class, 'show'])->name('enrollment-statistics.show');

// Public ALS Stories Routes
Route::get('/als-stories', [FrontendAlsStoryController::class, 'index'])->name('als-stories.index');
Route::get('/als-stories/{id}', [FrontendAlsStoryController::class, 'show'])->name('als-stories.show');

// Public ALS Implementers Routes
Route::get('/als-implementers', [FrontendAlsImplementerController::class, 'index'])->name('als-implementers.index');
Route::get('/als-implementers/{id}', [FrontendAlsImplementerController::class, 'show'])->name('als-implementers.show');

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
        
        // Modules Controller
        Route::get('/modules', [FrontendModulesController::class, 'index'])->name('modules');
        Route::get('/modules/{id}', [FrontendModulesController::class, 'show'])->name('modules.show');
    });

    // --- ADDED ELEMENTARY ROUTE ---
    Route::get('/elementary', [ElementaryFrontendController::class, 'index'])->name('elementary');

    // Junior High Route
    Route::get('/junior-high', [JuniorHighFrontendController::class, 'index'])->name('junior-high');
    
    // Senior High Route
    Route::get('/senior-high', [SeniorHighFrontendController::class, 'index'])->name('senior-high');
});

// --- 📍 INTERACTIVE MAP ROUTE (DIVISION DATA) ---
Route::get('/schools/map-directory', function () {
    return view('frontend.schools.map_directory');
})->name('schools.map');

// Public Procurement
Route::prefix('procurement/{category}')->name('procurement.')->group(function () {
    Route::get('/', [BidOpportunityController::class, 'index'])->name('index');
    Route::get('/{id}', [BidOpportunityController::class, 'show'])->name('show');
});

// NEW PUBLIC ROUTE FOR DYNAMIC PAGES
Route::get('/page/{slug}', [FrontendPageController::class, 'show'])->name('frontend.page');

// ==========================================
// SECURE ROUTES
// ==========================================

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('/procurement/file/{id}/{type}', [FileAccessController::class, 'show'])->name('procurement.file.access');

    // Protected Admin Management
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // --- ADDED PROFILE ROUTES HERE ---
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/verify', [ProfileController::class, 'verify'])->name('profile.verify'); // New Route
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Dashboard
        Route::middleware(['permission:dashboard'])->group(function () {
            Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        });

        // ADDED: Global Maintenance Toggle Route (Placed here so the bottom-left sidebar toggle works for admins)
        Route::post('/settings/toggle-maintenance', [SiteSettingController::class, 'toggleMaintenance'])->name('settings.toggle-maintenance');

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

        // --- ADMIN ROUTE FOR ORGANIZATIONAL CHART, SGOD, OSDS, CID & DIVISIONS ---
        Route::middleware(['permission:about'])->group(function () {
            Route::prefix('org-chart')->name('org_chart.')->group(function () {
                Route::get('/', [OrgChartAdminController::class, 'index'])->name('index');
                Route::post('/position', [OrgChartAdminController::class, 'storePosition'])->name('store');
                Route::delete('/position/{position}', [OrgChartAdminController::class, 'destroyPosition'])->name('destroy');
                
                Route::post('/position/{position}/assign', [OrgChartAdminController::class, 'assignSlot'])->name('assign');
                Route::delete('/assignment/{assignment}', [OrgChartAdminController::class, 'unassignSlot'])->name('unassign');
            });

            // Division Structures
            Route::get('/division-structures', [DivisionStructureController::class, 'index'])->name('division_structures.index');
            Route::post('/division-structures', [DivisionStructureController::class, 'store'])->name('division_structures.store');
            Route::get('/division-structures/{divisionStructure}/edit', [DivisionStructureController::class, 'edit'])->name('division_structures.edit');
            Route::put('/division-structures/{divisionStructure}', [DivisionStructureController::class, 'update'])->name('division_structures.update');
            Route::delete('/division-structures/{divisionStructure}', [DivisionStructureController::class, 'destroy'])->name('division_structures.destroy');
            Route::delete('/division-structures/{divisionStructure}/pdf/{pdfIndex}', [DivisionStructureController::class, 'destroyPdf'])->name('division_structures.destroy_pdf');

            // SGOD Management
            Route::resource('sgod', AdminSgodController::class)->except(['create', 'show', 'edit']);
            
            // OSDS Management
            Route::resource('osds', AdminOsdsController::class)->except(['create', 'show', 'edit']);

            // CID Management
            Route::resource('cid', AdminCidController::class)->except(['create', 'show', 'edit']);
        });

        // --- ADMIN ROUTE FOR DYNAMIC PAGES & PAGE SECTIONS ---
        Route::middleware(['permission:pages'])->group(function () {
            Route::resource('pages', AdminPageController::class);
            
            // <-- ADDED: Page Sections Route -->
            Route::resource('page-sections', PageSectionController::class)->except(['create', 'show', 'edit']);
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

        // Enrollment Statistics
        Route::middleware(['permission:curriculum'])->group(function () {
            Route::resource('enrollment-statistics', EnrollmentStatisticController::class)->except(['create', 'show', 'edit']);
        });

        // ALS Stories
        Route::middleware(['permission:curriculum'])->group(function () {
            Route::resource('als-stories', AdminAlsStoryController::class)->except(['create', 'show', 'edit']);
        });

        // ALS Implementers
        Route::middleware(['permission:curriculum'])->group(function () {
            Route::resource('als-implementers', AdminAlsImplementerController::class)->except(['create', 'show', 'edit']);
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

            // --- ADDED ELEMENTARY MANAGEMENT ROUTE ---
            Route::resource('elementary-management', ElementaryController::class)
                ->names('elementary')
                ->parameters(['elementary-management' => 'elementary'])
                ->except(['create', 'show', 'edit']);

            // JUNIOR HIGH MANAGEMENT ROUTES
            Route::resource('junior-high-management', JuniorHighController::class)
                ->names('junior_high')
                ->parameters(['junior-high-management' => 'juniorHigh'])
                ->except(['create', 'show', 'edit']);

            // SENIOR HIGH MANAGEMENT ROUTES
            Route::resource('senior-high-management', SeniorHighController::class)
                ->names('senior_high')
                ->parameters(['senior-high-management' => 'seniorHigh'])
                ->except(['create', 'show', 'edit']);
        });

        // User & Role Management
        Route::middleware(['permission:users'])->group(function () {
            // Users
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update'); 
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            
            // Roles
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
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
            Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
            Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
        });

        // Issuances
        Route::prefix('issuances')->name('issuances.')->group(function () {
            Route::get('/', function (\Illuminate\Http\Request $request) {
                $type = $request->query('type', 'advisory');
                $permissionRequired = match($type) {
                    'memorandum' => 'memoranda',
                    'hrmpsb'     => 'hrmpsb',
                    default      => 'advisories'
                };
                if (!auth()->user()->hasPermission($permissionRequired)) {
                    abort(403, 'Unauthorized Access.');
                }
                return app(\App\Http\Controllers\IssuanceController::class)->adminIndex($request);
            })->name('index');

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