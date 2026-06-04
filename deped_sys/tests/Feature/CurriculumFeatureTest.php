<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\CurriculumGuide;

class CurriculumFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_add_curriculum_guide()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // 2. Action: Post to the specific route for storing curriculum guides
        $response = $this->actingAs($admin)->post(route('admin.curriculum.guides.store'), [
            'title' => 'K to 12 Basic Education Curriculum Guide',
            'link' => 'https://example.com/k-to-12-guide'
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // 4. Verify it was actually saved in the correct database table
        $this->assertDatabaseHas('curriculum_guides', [
            'title' => 'K to 12 Basic Education Curriculum Guide',
            'link' => 'https://example.com/k-to-12-guide'
        ]);
    }
}