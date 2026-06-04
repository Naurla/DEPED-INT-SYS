<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Position;
use App\Models\PositionAssignment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class OrgChartFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_create_a_position_and_assign_an_official()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('superintendent.jpg');

        // 2. Action: Create Position
        $response = $this->actingAs($admin)->post('/admin/org-chart/position', [
            'name' => 'Schools Division Superintendent',
            'slots_count' => 1,
        ]);

        $position = Position::where('name', 'Schools Division Superintendent')->first();
        $this->assertNotNull($position);

        // 3. Action: Assign Official to Slot 1
        $assignResponse = $this->actingAs($admin)->post("/admin/org-chart/position/{$position->id}/assign", [
            'slot_index' => 1,
            'employee_name' => 'Dr. Jane Doe',
            'employee_position' => 'Superintendent',
            'employee_image' => $fakeImage,
        ]);

        // 4. Assertions
        $assignResponse->assertRedirect();
        
        $this->assertDatabaseHas('position_assignments', [
            'position_id' => $position->id,
            'employee_name' => 'Dr. Jane Doe',
        ]);

        $assignment = PositionAssignment::where('position_id', $position->id)->first();
        Storage::disk('public')->assertExists($assignment->employee_image);
    }
}