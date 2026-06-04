<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BannerFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_upload_a_home_banner()
    {
        // 1. Setup Data
        // Disable permission checks so the test handles core functionality cleanly
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);

        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $role->id, 
        ]);

        // Intercept file storage so test files don't bloat your real 'public/banners' folder
        Storage::fake('public');

        // Generate a fake image to satisfy the controller's validation rules
        $fakeImage = UploadedFile::fake()->image('welcome_banner.jpg');

        // 2. Perform Action
        // Post to the store route for banners. 
        // Note: If your web.php uses a /store suffix (e.g., /admin/banners/store), update this URL!
        $response = $this->actingAs($admin)->post('/admin/banners', [
            'image' => $fakeImage,
            'sort_order' => 1,
            'is_active' => 1, // 1 for true based on your controller's boolean validation
        ]);

        // 3. Assert Results
        // Verify no unexpected validation blocks occurred (like duplicate names or overlapping sort_orders)
        $response->assertSessionHasNoErrors(); 
        
        // Check for a standard post-creation redirect (back()->with('success', ...))
        $response->assertRedirect();
        
        // Assert the data safely landed inside the database table
        $this->assertDatabaseHas('banners', [
            'sort_order' => 1,
            'is_active' => 1,
        ]);

        // Verify the file was actually stored in the faked public disk using your custom naming convention
        $banner = Banner::first();
        Storage::disk('public')->assertExists($banner->image_path);
    }
}