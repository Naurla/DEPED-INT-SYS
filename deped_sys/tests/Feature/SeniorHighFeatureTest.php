<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\SeniorHighContent;

class SeniorHighFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_add_senior_high_content()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Fake the public storage disk so we don't save real files during tests
        Storage::fake('public');
        
        // Create a fake CSV file
        $fakeCsv = UploadedFile::fake()->create('shs_registry.csv', 100, 'text/csv');

        // 2. Action: Post to the correct resource route
        $response = $this->actingAs($admin)->post(route('admin.curriculum.senior_high.store'), [
            'title' => 'List of Public Senior High Schools',
            'content' => 'This document contains the official registry of all senior high schools in the division.',
            'csv_file' => $fakeCsv,
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // 4. Verify data persistence in the database
        $this->assertDatabaseHas('senior_high_contents', [
            'title' => 'List of Public Senior High Schools',
            'content' => 'This document contains the official registry of all senior high schools in the division.',
            'school_type' => 'public', 
        ]);
        
        // 5. Verify the file was successfully routed to the correct directory
        $content = SeniorHighContent::first();
        
        $this->assertNotNull($content->csv_path);
        $this->assertStringContainsString('senior_high/documents', $content->csv_path);
        Storage::disk('public')->assertExists($content->csv_path);
    }
}