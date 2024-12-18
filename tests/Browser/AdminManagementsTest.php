<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class AdminManagementsTest extends DuskTestCase
{
    /** @test */
    public function it_can_navigate_to_admin_page_and_create_a_new_admin()
    {
        Role::firstOrCreate(['name' => 'Super-Admin']);

        $superAdmin = User::whereHas('roles', function ($query) {
            $query->where('name', 'Super-Admin');
        })->first();

        $this->browse(function (Browser $browser) use ($superAdmin) {
            $browser->loginAs($superAdmin)
                ->visit(route('admin-managements.create'))
                ->type('firstName', 'Browser')
                ->type('lastName', 'Tester')
                ->type('email', 'browser.tester@example.com')
                ->type('password', 'Password123!')
                ->type('password_confirmation', 'Password123!')
                ->type('phoneNumber', '1234567890')
                ->type('position', 'Browse  r Test Admin')
                ->select('country_id', '1') // Assuming first country
                ->select('gender', 'male')
                ->type('dob', '1990-01-01')
                ->press('Submit')
                ->assertPathIs(route('admin-managements.index'))
                ->assertSee('Admin Browser Tester\'s account successfully added!');
        });
    }
}
