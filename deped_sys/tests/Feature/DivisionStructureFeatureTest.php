<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\DivisionStructure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DivisionStructureFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_create_a_division_structure()
    {
        // 1. Setup Data
        // Disable permission checks so the test handles core functionality cleanly
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

        // Generate fake files to satisfy the controller's validation rules
        $fakeImage = UploadedFile::fake()->image('office_front.jpg');
        $fakePdf1 = UploadedFile::fake()->create('org_chart.pdf', 150, 'application/pdf');
        $fakePdf2 = UploadedFile::fake()->create('contact_info.pdf', 100, 'application/pdf');

        // 2. Perform Action
        // Post to the store route for division structures
        $response = $this->actingAs($admin)->post('/admin/division-structures', [
            'name' => 'Curriculum Implementation Division (CID)',
            'order_no' => 2,
            'main_photo' => $fakeImage,
            'pdf_documents' => [
                $fakePdf1,
                $fakePdf2
            ] // Sent as an array to test the foreach loop in your controller
        ]);

        // 3. Assert Results
        // Verify no unexpected validation blocks occurred
        $response->assertSessionHasNoErrors(); 
        
        // Check for a standard post-creation redirect
        $response->assertRedirect(route('admin.division_structures.index'));
        
        // Assert the standard string data safely landed inside the database table
        $this->assertDatabaseHas('division_structures', [
            'name' => 'Curriculum Implementation Division (CID)',
            'type' => 'Division', // Verifies your controller's default override ($data['type'] = 'Division') worked
        ]);

        // Fetch the fresh record to assert JSON casting and file storage worked correctly
        $division = DivisionStructure::where('name', 'Curriculum Implementation Division (CID)')->first();
        
        // Verify the main photo was stored physically
        Storage::disk('public')->assertExists($division->main_photo);

        // Normalize JSON to array to test safely regardless of how Laravel cast it in your model
        $pdfs = is_string($division->pdf_documents) ? json_decode($division->pdf_documents, true) : $division->pdf_documents;

        // Verify the array was structured correctly by your controller logic
        $this->assertCount(2, $pdfs);
        $this->assertEquals('org_chart.pdf', $pdfs[0]['original_name']);
        $this->assertArrayHasKey('size', $pdfs[0]); 
        
        // Verify the PDFs were stored physically
        Storage::disk('public')->assertExists($pdfs[0]['path']);
        Storage::disk('public')->assertExists($pdfs[1]['path']);
    }
}