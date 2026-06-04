<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\EnrollmentStatistic;

class EnrollmentStatisticFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_add_enrollment_statistic()
    {
        // 1. Setup
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);
        
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $admin = User::factory()->create(['role_id' => $role->id]);

        // Fake the public storage disk so we don't save real files during tests
        Storage::fake('public');
        
        // Create fake files to test the upload logic
        $fakeImage = UploadedFile::fake()->image('chart.png');
        $fakePdf = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        // 2. Action: Post to the correct resource route
        $response = $this->actingAs($admin)->post(route('admin.enrollment-statistics.store'), [
            'title' => 'SY 2025-2026 Enrollment Data',
            'school_year' => '2025-2026',
            'content' => 'Overview of the enrollment figures for the current school year.',
            'image' => $fakeImage,
            'file' => $fakePdf,
        ]);

        // 3. Assertions
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // 4. Verify data persistence in the database
        $this->assertDatabaseHas('enrollment_statistics', [
            'title' => 'SY 2025-2026 Enrollment Data',
            'school_year' => '2025-2026',
            'content' => 'Overview of the enrollment figures for the current school year.',
        ]);
        
        // 5. Verify the files were successfully routed to the correct directories
        $statistic = EnrollmentStatistic::first();
        
        $this->assertNotNull($statistic->image_path);
        $this->assertStringContainsString('enrollment_statistics/images', $statistic->image_path);
        Storage::disk('public')->assertExists($statistic->image_path);
        
        $this->assertNotNull($statistic->file_path);
        $this->assertStringContainsString('enrollment_statistics/files', $statistic->file_path);
        Storage::disk('public')->assertExists($statistic->file_path);
    }
}