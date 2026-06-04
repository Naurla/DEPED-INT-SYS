<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\VisionMission;

class VisionMissionFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_update_vision_and_mission()
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

        // Create the initial Vision/Mission record since the controller updates VisionMission::first()
        VisionMission::create([
            'sections' => []
        ]);

        // 2. Perform Action
        // Post a payload representing multiple dynamic sections (Vision, Mission, Core Values)
        $response = $this->actingAs($admin)->post('/admin/vision-mission', [
            'sections' => [
                [
                    'title' => 'Our Vision',
                    'content' => '<p>We dream of Filipinos who passionately love their country.</p>'
                ],
                [
                    'title' => 'Our Mission',
                    'content' => '<p>To protect and promote the right of every Filipino to quality, equitable, culture-based, and complete basic education.</p>'
                ]
            ]
        ]);

        // 3. Assert Results
        // Verify no unexpected validation blocks occurred
        $response->assertSessionHasNoErrors(); 
        
        // Check for a standard post-update redirect
        $response->assertRedirect(route('admin.vision_mission.index'));
        
        // Fetch the fresh record to assert JSON casting worked correctly
        $updatedData = VisionMission::first();
        
        // Normalize JSON to array to test safely regardless of how Laravel cast it in your model
        $sections = is_string($updatedData->sections) 
            ? json_decode($updatedData->sections, true) 
            : $updatedData->sections;

        // Verify the arrays were saved properly and the controller logic executed
        $this->assertCount(2, $sections);
        $this->assertEquals('Our Vision', $sections[0]['title']);
        $this->assertEquals('<p>We dream of Filipinos who passionately love their country.</p>', $sections[0]['content']);
        $this->assertEquals('Our Mission', $sections[1]['title']);
    }
}