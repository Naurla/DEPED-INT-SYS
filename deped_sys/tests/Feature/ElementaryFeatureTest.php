<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\ElementaryContent;

class ElementaryFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_add_elementary_content()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Fake the public storage disk so we don't save real files during tests
        Storage::fake('public');
        
        // Create a fake CSV file
        $fakeCsv = UploadedFile::fake()->create('school_registry.csv', 100, 'text/csv');

        // 2. Action: Post to the correct resource route under the curriculum prefix
        $response = $this->actingAs($admin)->post(route('admin.curriculum.elementary.store'), [
            'title' => 'List of Public Elementary Schools',
            'content' => 'This document contains the official registry of all elementary schools in the division.',
            'csv_file' => $fakeCsv,
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // 4. Verify data persistence in the database
        $this->assertDatabaseHas('elementary_contents', [
            'title' => 'List of Public Elementary Schools',
            'content' => 'This document contains the official registry of all elementary schools in the division.',
            'school_type' => 'public', // The controller forces this value
        ]);
        
        // 5. Verify the file was successfully routed to the correct directory
        $content = ElementaryContent::first();
        
        $this->assertNotNull($content->csv_path);
        $this->assertStringContainsString('elementary/documents', $content->csv_path);
        Storage::disk('public')->assertExists($content->csv_path);
    }
}