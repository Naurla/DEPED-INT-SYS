<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\PageSection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PageSectionFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_create_a_page_section()
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

        // Intercept file storage so test files don't bloat your real 'public/page_sections' folder
        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('section_banner.jpg');

        // 2. Perform Action
        // Post to the store route for page sections
        $response = $this->actingAs($admin)->post('/admin/page-sections', [
            'display_location' => 'home',
            'type' => 'banner', // Testing the banner specifically to hit the file upload logic
            'title' => 'Welcome to Home',
            'content' => '<p>Some text</p>',
            'image' => $fakeImage,
            'sort_order' => 1,
            'is_active' => 1, // 1 for true
        ]);

        // 3. Assert Results
        $response->assertSessionHasNoErrors(); 
        $response->assertRedirect();
        
        $this->assertDatabaseHas('page_sections', [
            'display_location' => 'home',
            'type' => 'banner',
            'title' => 'Welcome to Home',
            'is_active' => 1,
        ]);

        // Verify the file was actually stored in the faked public disk
        $section = PageSection::first();
        Storage::disk('public')->assertExists($section->image_path);
    }

    public function test_administrator_can_reorder_page_sections()
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

        // Create two dummy sections in the database directly
        $section1 = PageSection::create([
            'display_location' => 'home',
            'type' => 'rich_text',
            'sort_order' => 1,
            'is_active' => 1,
        ]);

        $section2 = PageSection::create([
            'display_location' => 'home',
            'type' => 'rich_text',
            'sort_order' => 2,
            'is_active' => 1,
        ]);

        // 2. Perform Action
        // Post to the custom reorder JSON endpoint, flipping their positions
        $response = $this->actingAs($admin)->post('/admin/page-sections/reorder', [
            'order' => [
                ['id' => $section1->id, 'position' => 2],
                ['id' => $section2->id, 'position' => 1],
            ]
        ]);

        // 3. Assert Results
        // Expect a 200 OK and a JSON success message
        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Verify the database actually applied the swapped positions
        $this->assertDatabaseHas('page_sections', [
            'id' => $section1->id,
            'sort_order' => 2,
        ]);

        $this->assertDatabaseHas('page_sections', [
            'id' => $section2->id,
            'sort_order' => 1,
        ]);
    }
}