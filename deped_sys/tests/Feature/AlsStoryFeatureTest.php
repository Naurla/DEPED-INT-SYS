<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\AlsStory;

class AlsStoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_add_als_story()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Fake the public storage disk so we don't save real files during tests
        Storage::fake('public');
        
        // Create fake files to test the upload logic
        $fakeImage = UploadedFile::fake()->image('story-photo.jpg');
        $fakePdf = UploadedFile::fake()->create('story-document.pdf', 100, 'application/pdf');

        // 2. Action: Post to the correct resource route (with hyphen based on web.php)
        $response = $this->actingAs($admin)->post(route('admin.als-stories.store'), [
            'title' => 'Inspiring Journey of an ALS Graduate',
            'content' => 'This is the detailed story of a learner who overcame adversity to finish basic education.',
            'image' => $fakeImage,
            'file' => $fakePdf,
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // 4. Verify data persistence in the database
        $this->assertDatabaseHas('als_stories', [
            'title' => 'Inspiring Journey of an ALS Graduate',
            'content' => 'This is the detailed story of a learner who overcame adversity to finish basic education.',
        ]);
        
        // 5. Verify the files were successfully routed to the correct directories
        $story = AlsStory::first();
        
        $this->assertNotNull($story->image_path);
        $this->assertStringContainsString('als_stories/images', $story->image_path);
        Storage::disk('public')->assertExists($story->image_path);
        
        $this->assertNotNull($story->file_path);
        $this->assertStringContainsString('als_stories/files', $story->file_path);
        Storage::disk('public')->assertExists($story->file_path);
    }
}