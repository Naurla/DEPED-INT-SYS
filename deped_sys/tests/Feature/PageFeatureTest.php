<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Page;

class PageFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_create_a_dynamic_page()
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

        // 2. Perform Action
        // Post to the resourceful store route for pages
        $response = $this->actingAs($admin)->post('/admin/pages', [
            'title' => 'About Our New Division',
            'content' => '<p>Welcome to the new division page.</p>',
            'layout_template' => 'full_width',
            
            // Tests the controller's logic that strips "menu_" and assigns it to 'menu_location'
            'parent_selection' => 'menu_header_main', 
            
            'show_in_nav' => 'on',
            
            // Tests the formatVideosArray method inside your controller
            'featured_videos' => [
                [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'shape' => 'landscape'
                ]
            ]
        ]);

        // 3. Assert Results
        // Verify no unexpected validation blocks occurred
        $response->assertSessionHasNoErrors(); 
        
        // Check for a standard post-creation redirect back to the index page
        $response->assertRedirect(route('admin.pages.index'));
        
        // Assert the standard string/boolean data safely landed inside the database table
        $this->assertDatabaseHas('pages', [
            'title' => 'About Our New Division',
            'layout_template' => 'full_width',
            'menu_location' => 'header_main', // Verifies the 'menu_' prefix was stripped correctly
            'show_in_nav' => 1,
        ]);

        // Fetch the fresh record to assert JSON casting worked correctly for the videos
        $page = Page::where('title', 'About Our New Division')->first();
        
        // Normalize JSON to array to test safely regardless of how Laravel cast it in your model
        $videos = is_string($page->featured_videos) ? json_decode($page->featured_videos, true) : $page->featured_videos;

        // Verify the array was cleaned and saved properly
        $this->assertCount(1, $videos);
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $videos[0]['url']);
        $this->assertEquals('landscape', $videos[0]['shape']);
    }
}