<?php

namespace Tests\Feature;

use App\Models\CompanyAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RegistrationPermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that registration works even when permissions don't exist initially.
     * The system should auto-create missing permissions.
     */
    public function test_registration_creates_permissions_automatically(): void
    {
        // Ensure permissions table is empty
        Permission::query()->delete();
        $this->assertEquals(0, Permission::count(), 'Permissions should be empty at start');

        // Attempt registration
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Test Company',
            'g-recaptcha-response' => 'test-token', // Will be skipped in local env
        ]);

        // Registration should succeed (redirect to verification page)
        $response->assertRedirect(route('verification.notice'));

        // User should be created
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        // Company should be created
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('company_accounts', [
            'user_id' => $user->id,
            'company_name' => 'Test Company',
        ]);

        // Permissions should have been auto-created
        $this->assertGreaterThan(0, Permission::count(), 'Permissions should be auto-created');
        
        // Verify key permissions exist
        $this->assertDatabaseHas('permissions', ['name' => 'document-list']);
        $this->assertDatabaseHas('permissions', ['name' => 'role-list']);
        $this->assertDatabaseHas('permissions', ['name' => 'user-list']);
    }

    /**
     * Test that company-specific roles are created during registration.
     */
    public function test_registration_creates_company_roles(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Acme Corp',
            'g-recaptcha-response' => 'test-token',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'john@example.com')->first();
        $company = CompanyAccount::where('user_id', $user->id)->first();

        // Verify company-specific roles exist
        $this->assertDatabaseHas('roles', [
            'name' => 'company-admin',
            'company_id' => $company->id,
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'user',
            'company_id' => $company->id,
        ]);

        // Verify user has company-admin role
        $this->assertTrue($user->hasRole('company-admin'));
    }
}
