<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile; // Required for fake files
use Illuminate\Support\Facades\Storage; // Required to fake the storage disk

class AdvisoryFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_upload_division_advisory()
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

        // Intercept file storage so test files don't bloat your real application folder
        Storage::fake('public');

        // Generate fake files that pass your controller's validation rules
        $fakeImage = UploadedFile::fake()->image('banner.jpg');
        $fakePdf = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        // 2. Perform Action
        $response = $this->actingAs($admin)->post('/admin/advisories/store', [
            'title' => 'Division Memorandum No. 0281, s. 2026',
            'image' => $fakeImage,
            'pdf' => $fakePdf,
        ]);

        // 3. Assert Results
        // This ensures the test fails loudly if validation fails again
        $response->assertSessionHasNoErrors(); 
        
        $response->assertRedirect();
        
        // Assert the data actually hit the database (Matches your AdvisoryController setup)
        $this->assertDatabaseHas('advisories', [
            'title' => 'Division Memorandum No. 0281, s. 2026',
        ]);
    }
}