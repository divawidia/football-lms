<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\Player;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $faker = Factory::create('id_ID');

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'player']);

        $role2 = Role::create(['name' => 'admin']);

        $role4 = Role::create(['name' => 'coach']);

        $role3 = Role::create(['name' => 'Super-Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider


        // create demo users
        $user = User::factory()->has(Player::factory(), 'player')->create([
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'email' => 'player@example.com',
            'password' => bcrypt('player12345'),
            'foto' => 'images/undefined-user.png',
            'dob' => $faker->date,
            'gender' => 'male',
            'address' => $faker->address,
            'state_id' => 1,
            'city_id' => 1,
            'country_id' => 1,
            'zipCode' => '80361',
            'phoneNumber' => $faker->phoneNumber,
            'status' => '1',
            'academyId' => 1,
        ]);
        $user->assignRole($role1);

        $user2 = User::factory()->has(Admin::factory(), 'admin')->create([
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'email' => 'admin@example.com',
            'password' => bcrypt('admin12345'),
            'foto' => 'images/undefined-user.png',
            'dob' => $faker->date,
            'gender' => 'male',
            'address' => $faker->address,
            'state_id' => 1,
            'city_id' => 1,
            'country_id' => 1,
            'zipCode' => '80361',
            'phoneNumber' => $faker->phoneNumber,
            'status' => '1',
            'academyId' => 1,
        ]);
        $user2->assignRole($role2);

        $user3 = User::factory()->has(Admin::factory(), 'admin')->create([
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'email' => 'superadmin@example.com',
            'password' => bcrypt('superadmin12345'),
            'foto' => 'images/undefined-user.png',
            'dob' => $faker->date,
            'gender' => 'male',
            'address' => $faker->address,
            'state_id' => 1,
            'city_id' => 1,
            'country_id' => 1,
            'zipCode' => '80361',
            'phoneNumber' => $faker->phoneNumber,
            'status' => '1',
            'academyId' => 1,
        ]);
        $user3->assignRole($role3);

        $user4 = User::factory()->has(Coach::factory(), 'coach')->create([
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'email' => 'coach@example.com',
            'password' => bcrypt('coach12345'),
            'foto' => 'images/undefined-user.png',
            'dob' => $faker->date,
            'gender' => 'male',
            'address' => $faker->address,
            'state_id' => 1,
            'city_id' => 1,
            'country_id' => 1,
            'zipCode' => '80361',
            'phoneNumber' => $faker->phoneNumber,
            'status' => '1',
            'academyId' => 1,
        ]);
        $user4->assignRole($role4);
    }
}
