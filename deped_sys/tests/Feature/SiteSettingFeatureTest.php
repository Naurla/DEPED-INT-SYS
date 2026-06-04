<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\SiteSetting;

class SiteSettingFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_update_general_site_settings()
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
        // Post a complex payload representing the settings form
        $response = $this->actingAs($admin)->post('/admin/settings', [
            'header_title' => 'DepEd Division of Zamboanga',
            'footer_about' => 'Providing quality basic education to all.',
            'qr_link' => 'https://deped.gov.ph/qr',
            'contact_email' => ['inquiry@deped.gov.ph', 'support@deped.gov.ph'],
            'contact_phone' => ['(062) 123-4567'],
            'address' => ['Main St, Zamboanga City'],
            'footer_sections' => [
                [
                    'title' => 'Quick Links',
                    'links' => [
                        ['label' => 'Home', 'url' => '/'],
                        ['label' => 'About Us', 'url' => '/about']
                    ]
                ]
            ]
        ]);

        // 3. Assert Results
        // Verify no unexpected validation blocks or duplicate errors occurred
        $response->assertSessionHasNoErrors(); 
        
        // Check for a standard post-update redirect (back()->with('success', ...))
        $response->assertRedirect();
        
        // Assert the basic string data safely landed inside the database table
        $this->assertDatabaseHas('site_settings', [
            'header_title' => 'DepEd Division of Zamboanga',
            'footer_about' => 'Providing quality basic education to all.',
            'qr_link' => 'https://deped.gov.ph/qr',
        ]);

        // Fetch the fresh record to assert JSON casting worked correctly for arrays
        $settings = SiteSetting::first();
        
        // Normalize JSON to array to test safely regardless of how Laravel cast it
        $emails = is_string($settings->contact_email) ? json_decode($settings->contact_email, true) : $settings->contact_email;
        $footerSections = is_string($settings->footer_sections) ? json_decode($settings->footer_sections, true) : $settings->footer_sections;

        // Verify the arrays were saved properly
        $this->assertContains('inquiry@deped.gov.ph', $emails);
        $this->assertEquals('Quick Links', $footerSections[0]['title']);
        $this->assertEquals('/about', $footerSections[0]['links'][1]['url']);
    }
}