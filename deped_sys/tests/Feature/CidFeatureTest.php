<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Cid;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CidFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_upload_a_cid_chart()
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

        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('cid_chart.jpg');

        // 2. Perform Action
        $response = $this->actingAs($admin)->post('/admin/cid', [
            'title' => 'Division CID Organizational Chart 2026',
            'description' => 'Detailed chart of the Curriculum Implementation Division.',
            'image' => $fakeImage,
        ]);

        // 3. Assert Results
        $response->assertSessionHasNoErrors(); 
        $response->assertRedirect();
        
        $this->assertDatabaseHas('cids', [
            'title' => 'Division CID Organizational Chart 2026',
            'description' => 'Detailed chart of the Curriculum Implementation Division.',
        ]);

        // Verify the file was stored
        $cid = Cid::first();
        Storage::disk('public')->assertExists($cid->image_path);
    }

    public function test_administrator_cannot_upload_duplicate_cid_title()
    {
        // 1. Setup Data
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        $admin = User::factory()->create([
            'role_id' => Role::create(['name' => 'Admin', 'slug' => 'admin'])->id,
        ]);

        Cid::create([
            'title' => 'Existing Chart',
            'image_path' => 'cid/images/old.jpg'
        ]);

        // 2. Perform Action
        $response = $this->actingAs($admin)->post('/admin/cid', [
            'title' => 'Existing Chart', // Duplicate title
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]);

        // 3. Assert Results
        $response->assertSessionHasErrors(['title']);
    }
}