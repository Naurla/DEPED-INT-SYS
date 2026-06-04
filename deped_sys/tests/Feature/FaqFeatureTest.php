<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Faq;

class FaqFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_add_faq()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // 2. Action: Post to the FAQ store route
        // Notice that 'answer' is now passed as an array to satisfy validation
        $response = $this->actingAs($admin)->post(route('admin.faq.store'), [
            'question' => 'What are the core values required for professional decision-making?',
            'answer' => [
                'Ethics is the foundation for professional decision-making within the division.',
                'Integrity and transparency are also highly valued.'
            ],
            'is_active' => 1
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.faq.index'));
        
        // 4. Verify it was actually saved in the database
        // The controller uses implode("\n", ...) to combine the array into a string
        $expectedAnswerStr = "Ethics is the foundation for professional decision-making within the division.\nIntegrity and transparency are also highly valued.";

        $this->assertDatabaseHas('faqs', [
            'question' => 'What are the core values required for professional decision-making?',
            'answer' => $expectedAnswerStr,
            'is_active' => 1
        ]);
    }
}