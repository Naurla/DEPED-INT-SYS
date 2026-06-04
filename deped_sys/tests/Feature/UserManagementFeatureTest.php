<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreatedMail;

class UserManagementFeatureTest extends TestCase
{
    use RefreshDatabase; 

    public function test_administrator_can_create_a_new_user()
    {
        // 1. Setup Data
        // Disable permission checks so the test handles core functionality cleanly
        $this->withoutMiddleware([\App\Http\Middleware\CheckPermission::class]);

        $superAdminRole = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $superAdminRole->id, 
        ]);

        // Create a secondary role to assign to the new user
        $staffRole = Role::create([
            'name' => 'Division Staff',
            'slug' => 'division-staff',
        ]);

        // IMPORTANT: Fake the mailer so actual emails aren't sent during testing
        Mail::fake();

        // 2. Perform Action
        // Post to the store route for users. 
        // Note: If your web.php uses a /store suffix (e.g., /admin/users/store), update this URL.
        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'New Employee',
            'email' => 'employee@deped.gov.ph',
            'role_id' => $staffRole->id,
        ]);

        // 3. Assert Results
        // Verify no unexpected validation blocks occurred
        $response->assertSessionHasNoErrors(); 
        
        // Check for a standard post-creation redirect
        $response->assertRedirect();
        
        // Assert the data safely landed inside the database table with the correct flags
        $this->assertDatabaseHas('users', [
            'name' => 'New Employee',
            'email' => 'employee@deped.gov.ph', // The controller forces this to lowercase
            'role_id' => $staffRole->id,
            'requires_password_change' => 1, // Asserts the controller logic worked
        ]);

        // Assert that the email containing the temporary password was generated and sent to the right person
        Mail::assertSent(UserCreatedMail::class, function ($mail) {
            return $mail->hasTo('employee@deped.gov.ph');
        });
    }
}