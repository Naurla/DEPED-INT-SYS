<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\SiteSetting;

class SiteMaintenanceFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_update_site_maintenance_settings()
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

        $setting = SiteSetting::create([
            'is_maintenance_mode' => false,
            'disabled_pages' => json_encode([]),
        ]);

        // 2. Perform Action
        // FIX 1: Updated URL to exactly match web.php: /admin/settings/toggle-maintenance
        $response = $this->actingAs($admin)->post('/admin/settings/toggle-maintenance', [
            'is_maintenance_mode' => 1, 
            'disabled_pages' => ['home', 'vision_mission', 'sgod'], 
        ]);

        // 3. Assert Results
        $response->assertSessionHasNoErrors(); 
        
        // FIX 2: Check for a successful JSON response instead of a redirect
        $response->assertOk(); 
        $response->assertJson(['success' => true]);
        
        // Assert the database was updated 
        $this->assertDatabaseHas('site_settings', [
            'id' => $setting->id,
            'is_maintenance_mode' => 1,
        ]);

        $updatedSetting = SiteSetting::find($setting->id);
        
        $disabledPages = is_string($updatedSetting->disabled_pages) 
            ? json_decode($updatedSetting->disabled_pages, true) 
            : $updatedSetting->disabled_pages;

        $this->assertContains('home', $disabledPages);
        $this->assertContains('vision_mission', $disabledPages);
    }
}