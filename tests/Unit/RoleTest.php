<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    private Role $role;
    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Role::firstOrCreate(['name' => 'admin']);
        $this->player = Role::firstOrCreate(['name' => 'player']);
        $this->coach = Role::firstOrCreate(['name' => 'coach']);
    }
    public function test_user_has_correct_role()
    {
        // Create a user and assign a role
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Assert the user has the correct role
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('player'));
    }
}
