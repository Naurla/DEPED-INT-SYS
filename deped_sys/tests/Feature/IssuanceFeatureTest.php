<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class IssuanceFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_create_an_issuance()
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

        // 2. Perform Action
        // FIX: Removed '/store' from the URL to match web.php
        $response = $this->actingAs($admin)->post('/admin/issuances', [
            'title' => 'Division Order No. 012, s. 2026',
            'type'  => 'memorandum', // Usually required by issuance controllers
            'date'  => '2026-06-04',
            'link'  => 'https://example.gov.ph/issuances/order-012',
        ]);

        // 3. Assert Results
        $response->assertSessionHasNoErrors(); 
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('issuances', [
            'title' => 'Division Order No. 012, s. 2026',
            'link' => 'https://example.gov.ph/issuances/order-012',
        ]);
    }
}