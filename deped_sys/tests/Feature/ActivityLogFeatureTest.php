<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class ActivityLogFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_super_admin_can_view_activity_logs()
    {
        // 1. Setup Data
        // Disable general permission middleware to test the controller's specific role check
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);

        // We MUST force the ID to 1 to pass the controller's strict role_id check
        $superAdminRole = Role::create([
            'id' => 1, 
            'name' => 'Super Admin',
            'slug' => 'super-admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $superAdminRole->id, 
        ]);

        // 2. Perform Action
        // Note: Check your web.php if this route differs (e.g., /admin/activity_logs or /admin/logs)
        $response = $this->actingAs($admin)->get('/admin/activity-logs');

        // 3. Assert Results
        // Verify the user is allowed in and the correct view is loaded with the required data
        $response->assertOk(); 
        $response->assertViewIs('admin.activity_logs.index');
        $response->assertViewHasAll(['logs', 'activeModule']);
    }

    public function test_non_super_admin_is_forbidden_from_viewing_activity_logs()
    {
        // 1. Setup Data
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);

        // Create a role with an ID other than 1
        $staffRole = Role::create([
            'id' => 2, 
            'name' => 'Division Staff',
            'slug' => 'division-staff',
        ]);

        $staff = User::factory()->create([
            'role_id' => $staffRole->id, 
        ]);

        // 2. Perform Action
        $response = $this->actingAs($staff)->get('/admin/activity-logs');

        // 3. Assert Results
        // Verify the controller aborts with a 403 Forbidden status
        $response->assertForbidden();
    }
}