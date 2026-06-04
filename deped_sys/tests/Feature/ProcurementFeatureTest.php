<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile; 
use Illuminate\Support\Facades\Storage; 

class ProcurementFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_upload_procurement_document()
    {
        // 1. Setup Data
        // Disable the permission middleware for the dummy user
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);

        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $role->id, 
        ]);

        // Intercept file storage so test files don't bloat your real application folder
        Storage::fake('public');

        // Generate a fake PDF to satisfy the controller's validation rules
        $fakePdf = UploadedFile::fake()->create('procurement_document.pdf', 100, 'application/pdf');

        // Define the category we want to test
        $category = 'bid-opportunities';

        // 2. Perform Action
        // Post to the dynamic category route
        $response = $this->actingAs($admin)->post("/admin/procurement/{$category}", [
            'title' => 'Invitation to Bid for IT Equipment 2026',
            'description' => 'Procurement of new laptops for the division.',
            'pdf_file' => $fakePdf,
            'date' => '2026-06-15',
        ]);

        // 3. Assert Results
        // Ensure there are no validation errors
        $response->assertSessionHasNoErrors(); 
        
        // Ensure the controller redirects back to the index page for that category
        $response->assertRedirect(route('admin.procurement.index', $category));
        
        // Assert the data actually hit the bid_opportunities table
        $this->assertDatabaseHas('bid_opportunities', [
            'title' => 'Invitation to Bid for IT Equipment 2026',
            'category' => 'bid-opportunities',
        ]);
    }
}