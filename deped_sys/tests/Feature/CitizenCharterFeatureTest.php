<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\CitizenCharter;

class CitizenCharterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_update_citizen_charter_content()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Ensure record exists
        CitizenCharter::create(['sections' => []]);

        // 2. Action: Use the exact route name found in your list
        // Note: Using 'post' because your route:list showed POST
        $response = $this->actingAs($admin)->post(route('admin.citizen_charter.update'), [
            'sections' => [
                [
                    'title' => 'Frontline Services',
                    'content' => 'Processing of requests within 3 days.'
                ],
                [
                    'title' => 'Feedback Mechanism',
                    'content' => 'Please provide feedback at the public assistance desk.'
                ]
            ]
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $updatedData = CitizenCharter::first();
        $sections = is_string($updatedData->sections) 
            ? json_decode($updatedData->sections, true) 
            : $updatedData->sections;

        $this->assertCount(2, $sections);
        $this->assertEquals('Frontline Services', $sections[0]['title']);
    }
}