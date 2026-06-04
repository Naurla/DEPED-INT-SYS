<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\SiteLogo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SiteLogoFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_upload_a_header_or_footer_logo()
    {
        // 1. Setup Data
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);

        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $role->id, 
        ]);

        // Intercept file storage
        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('deped_seal.png');

        // 2. Perform Action
        $response = $this->actingAs($admin)->post('/admin/logos', [
            'name' => 'DepEd Official Seal',
            'image' => $fakeImage,
            'position' => 'left', // Represents header left
            'order' => 1,
            'is_active' => 1, 
        ]);

        // 3. Assert Results
        $response->assertSessionHasNoErrors(); 
        $response->assertRedirect();
        
        $this->assertDatabaseHas('site_logos', [
            'name' => 'DepEd Official Seal',
            'position' => 'left',
            'order' => 1,
            'is_active' => 1,
        ]);

        // Verify the file was physically saved to the storage disk
        $logo = SiteLogo::first();
        Storage::disk('public')->assertExists($logo->image_path);
    }

    public function test_system_prevents_duplicate_position_and_order_conflicts()
    {
        // 1. Setup Data
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);

        $admin = User::factory()->create([
            'role_id' => Role::create(['name' => 'Admin', 'slug' => 'admin'])->id, 
        ]);

        // Pre-create a logo occupying "footer_left" at order 1
        SiteLogo::create([
            'name' => 'Existing Footer Logo',
            'image_path' => 'logos/existing.png',
            'position' => 'footer_left',
            'order' => 1,
            'is_active' => 1,
        ]);

        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('new_logo.png');

        // 2. Perform Action (Try to upload a new logo to the exact same spot)
        $response = $this->actingAs($admin)->post('/admin/logos', [
            'name' => 'New Partner Logo',
            'image' => $fakeImage,
            'position' => 'footer_left', // Conflict!
            'order' => 1,                // Conflict!
        ]);

        // 3. Assert Results
        // Your controller explicitly returns a custom 'error' session flash on conflict
        $response->assertSessionHas('error');
        
        // Assert the new logo was blocked and never hit the database
        $this->assertDatabaseMissing('site_logos', [
            'name' => 'New Partner Logo',
        ]);
    }
}