<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\LearningMaterials;

class LearningMaterialFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_add_learning_material()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Fake the public storage disk so we don't save real files during tests
        Storage::fake('public');
        
        // Create a fake PDF file
        $fakePdf = UploadedFile::fake()->create('handout.pdf', 100, 'application/pdf');

        // 2. Action: Post to the correct resource route
        $response = $this->actingAs($admin)->post(route('admin.learning-materials.store'), [
            'title' => 'Introduction to Computer Science Handout',
            'description' => 'A comprehensive guide for beginners.',
            'file' => $fakePdf
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // 4. Verify data persistence in the correct database table (k12_learning_materials)
        $this->assertDatabaseHas('k12_learning_materials', [
            'title' => 'Introduction to Computer Science Handout',
            'description' => 'A comprehensive guide for beginners.',
            'file_type' => 'pdf'
        ]);
        
        // 5. Verify the file was actually "saved" to the faked storage
        $material = LearningMaterials::first();
        $this->assertNotNull($material->file_path);
        Storage::disk('public')->assertExists($material->file_path);
    }
}