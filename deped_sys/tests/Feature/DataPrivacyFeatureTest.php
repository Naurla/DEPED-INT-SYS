<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\DataPrivacy;

class DataPrivacyFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_update_data_privacy_content()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Ensure a record exists (controller uses DataPrivacy::firstOrCreate)
        DataPrivacy::create(['sections' => []]);

        // 2. Action: Post dynamic sections
        $response = $this->actingAs($admin)->post('/admin/data-privacy', [
            'sections' => [
                [
                    'title' => 'Personal Information Collection',
                    'content' => 'We collect data for educational processing.'
                ],
                [
                    'title' => 'Data Protection Measures',
                    'content' => 'Data is encrypted and stored securely.'
                ]
            ]
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $updatedData = DataPrivacy::first();
        $sections = is_string($updatedData->sections) 
            ? json_decode($updatedData->sections, true) 
            : $updatedData->sections;

        $this->assertCount(2, $sections);
        $this->assertEquals('Personal Information Collection', $sections[0]['title']);
    }
}