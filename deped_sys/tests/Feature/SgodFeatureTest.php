<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Sgod;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SgodFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_upload_sgod_content()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('sgod_banner.jpg');

        // 2. Action: Post content to the SGOD store route
        // Ensure this route matches your web.php definition (e.g., /admin/sgod)
        $response = $this->actingAs($admin)->post('/admin/sgod', [
            'title' => 'SGOD Division Functional Division',
            'description' => 'Supporting school improvement and governance.',
            'image' => $fakeImage,
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('sgods', [
            'title' => 'SGOD Division Functional Division',
            'description' => 'Supporting school improvement and governance.',
        ]);

        $sgod = Sgod::first();
        Storage::disk('public')->assertExists($sgod->image_path);
    }
}