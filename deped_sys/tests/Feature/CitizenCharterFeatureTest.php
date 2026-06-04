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

        // Ensure a record exists
        CitizenCharter::create([
            'title' => 'Initial Title',
            'content' => 'Initial Content'
        ]);

        // 2. Action: Post the correct fields (title, content, links)
        $response = $this->actingAs($admin)->post(route('admin.citizen_charter.update'), [
            'title' => 'Updated Frontline Services',
            'content' => 'Processing of requests within 3 days.',
            'links' => [
                [
                    'name' => 'Feedback Portal',
                    'url' => 'https://example.com/feedback'
                ],
                [
                    'name' => 'Downloadable Forms',
                    'url' => 'https://example.com/forms'
                ]
            ]
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $updatedData = CitizenCharter::first();

        // Assert the standard text fields were updated
        $this->assertEquals('Updated Frontline Services', $updatedData->title);
        $this->assertEquals('Processing of requests within 3 days.', $updatedData->content);
        
        // Assert the links array was updated correctly
        $this->assertCount(2, $updatedData->links);
        $this->assertEquals('Feedback Portal', $updatedData->links[0]['name']);
        $this->assertEquals('https://example.com/feedback', $updatedData->links[0]['url']);
    }
}