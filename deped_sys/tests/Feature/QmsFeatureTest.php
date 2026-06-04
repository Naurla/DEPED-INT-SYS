<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Qms;

class QmsFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_update_qms_information()
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

        // Create the initial QMS record since the controller updates Qms::first()
        $qms = Qms::create([
            'sections' => []
        ]);

        // 2. Perform Action
        // Post a payload representing multiple dynamic sections
        $response = $this->actingAs($admin)->post('/admin/qms', [
            'sections' => [
                [
                    'title' => 'Quality Policy',
                    'content' => '<p>We are committed to providing quality basic education.</p>'
                ],
                [
                    'title' => 'Scope of the QMS',
                    'content' => '<p>This covers all operations within the Division Office.</p>'
                ]
            ]
        ]);

        // 3. Assert Results
        // Verify no unexpected validation blocks occurred
        $response->assertSessionHasNoErrors(); 
        
        // Check for a standard post-update redirect
        $response->assertRedirect(route('admin.qms.index'));
        
        // Fetch the fresh record to assert JSON casting worked correctly
        $updatedQms = Qms::first();
        
        // Normalize JSON to array to test safely regardless of how Laravel cast it in your model
        $sections = is_string($updatedQms->sections) 
            ? json_decode($updatedQms->sections, true) 
            : $updatedQms->sections;

        // Verify the arrays were saved properly and the controller logic executed
        $this->assertCount(2, $sections);
        $this->assertEquals('Quality Policy', $sections[0]['title']);
        $this->assertEquals('<p>We are committed to providing quality basic education.</p>', $sections[0]['content']);
        $this->assertEquals('Scope of the QMS', $sections[1]['title']);
    }
}