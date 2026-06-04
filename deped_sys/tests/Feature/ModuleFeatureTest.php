<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Modules;

class ModuleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_add_module()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Fake the public storage disk so we don't save real files during tests
        Storage::fake('public');
        
        // Create fake files to test the upload logic
        $fakePdf = UploadedFile::fake()->create('communication_skills.pdf', 100, 'application/pdf');
        $fakeImage = UploadedFile::fake()->image('module_cover.jpg');

        // 2. Action: Post to the correct resource route
        $response = $this->actingAs($admin)->post(route('admin.modules.store'), [
            'title' => 'ALS Module 1: Communication Skills',
            'description' => 'A comprehensive guide to basic communication and writing.',
            'file' => $fakePdf,
            'image' => $fakeImage,
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // 4. Verify data persistence in the CORRECT database table (k12_modules)
        $this->assertDatabaseHas('k12_modules', [
            'title' => 'ALS Module 1: Communication Skills',
            'description' => 'A comprehensive guide to basic communication and writing.',
            'file_type' => 'pdf'
        ]);
        
        // 5. Verify the files were successfully routed to the correct directories
        $module = Modules::first();
        
        // Check Document
        $this->assertNotNull($module->file_path);
        $this->assertStringContainsString('modules/files', $module->file_path);
        Storage::disk('public')->assertExists($module->file_path);
        
        // Check Cover Image
        $this->assertNotNull($module->image_path);
        $this->assertStringContainsString('modules/images', $module->image_path);
        Storage::disk('public')->assertExists($module->image_path);
    }
}