<?php

namespace Tests\Browser;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     */
    public function test_user_can_login(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()
            ->has(Admin::factory(), 'admin')
            ->create();
        $admin->assignRole('admin');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit('/login')
                ->assertSee('Login to your account')
                ->type('email', $admin->email)
                ->type('password', $admin->password)
                ->press('button[type="submit"]')
                ->assertSee('These credentials do not match our records.');
        });
    }
}
