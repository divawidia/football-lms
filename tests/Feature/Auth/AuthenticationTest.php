<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        // Create roles
        Role::create(['name' => 'admin']);

        $admin = User::factory()
            ->has(Admin::factory(), 'admin')
            ->create(['email' => 'admin@example.com', 'password' => bcrypt('password')]);

        $admin->assignRole('admin');

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin-dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()
            ->has(Admin::factory(), 'admin')
            ->create();

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        // Create roles
        Role::create(['name' => 'admin']);

        $admin = User::factory()
            ->has(Admin::factory(), 'admin')
            ->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
