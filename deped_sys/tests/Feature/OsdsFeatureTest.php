<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Osds;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class OsdsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_upload_osds_content()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('osds_banner.jpg');

        // 2. Action: Post content to the OSDS store route
        // Ensure this route matches your web.php definition (e.g., /admin/osds)
        $response = $this->actingAs($admin)->post('/admin/osds', [
            'title' => 'OSDS Division Functional Division',
            'description' => 'Supporting the division goals through effective management.',
            'image' => $fakeImage,
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('osds', [
            'title' => 'OSDS Division Functional Division',
            'description' => 'Supporting the division goals through effective management.',
        ]);

        $osds = Osds::first();
        Storage::disk('public')->assertExists($osds->image_path);
    }
}