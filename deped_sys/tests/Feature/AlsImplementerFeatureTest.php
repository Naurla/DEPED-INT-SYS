<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\AlsImplementer;

class AlsImplementerFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_add_als_implementer()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Fake the public storage disk so we don't save real files during tests
        Storage::fake('public');
        
        // Create fake files to test the upload logic
        $fakeImage = UploadedFile::fake()->image('implementer-profile.jpg');
        $fakePdf = UploadedFile::fake()->create('implementer-report.pdf', 100, 'application/pdf');

        // 2. Action: Post to the correct resource route
        $response = $this->actingAs($admin)->post(route('admin.als-implementers.store'), [
            'title' => 'Juan Dela Cruz - January 2026',
            'content' => 'Recognized for exceptional dedication to the Alternative Learning System in the rural division.',
            'image' => $fakeImage,
            'file' => $fakePdf,
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // 4. Verify data persistence in the database
        $this->assertDatabaseHas('als_implementers', [
            'title' => 'Juan Dela Cruz - January 2026',
            'content' => 'Recognized for exceptional dedication to the Alternative Learning System in the rural division.',
        ]);
        
        // 5. Verify the files were successfully routed to the correct directories
        $implementer = AlsImplementer::first();
        
        $this->assertNotNull($implementer->image_path);
        $this->assertStringContainsString('als_implementers/images', $implementer->image_path);
        Storage::disk('public')->assertExists($implementer->image_path);
        
        $this->assertNotNull($implementer->file_path);
        $this->assertStringContainsString('als_implementers/files', $implementer->file_path);
        Storage::disk('public')->assertExists($implementer->file_path);
    }
}