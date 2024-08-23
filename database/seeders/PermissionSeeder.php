<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Coach;
use App\Models\Player;
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

        // create permissions
        Permission::create(['name' => 'edit players']);
        Permission::create(['name' => 'view players']);
        Permission::create(['name' => 'delete players']);
        Permission::create(['name' => 'create players']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'player']);
        $role1->givePermissionTo('edit players');
        $role1->givePermissionTo('view players');

        $role2 = Role::create(['name' => 'admin']);
        $role2->givePermissionTo('create players');
        $role2->givePermissionTo('edit players');
        $role2->givePermissionTo('view players');
        $role2->givePermissionTo('delete players');

        $role4 = Role::create(['name' => 'coach']);
        $role4->givePermissionTo('view players');

        $role3 = Role::create(['name' => 'Super-Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::factory()->create([
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'email' => 'player@example.com',
            'password' => bcrypt('player12345'),
            'dob' => $faker->date,
            'gender' => 'male',
            'address' => $faker->address,
            'state' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'country' => 'Indonesia',
            'zipCode' => '80361',
            'phoneNumber' => $faker->phoneNumber,
            'status' => '1',
            'academyId' => 1,
        ]);
        $user->assignRole($role1);
        $player = Player::factory()->create([
            'position' => 'Center Back',
            'skill' => 'intermediate',
            'strongFoot' => 'right',
            'height' => 175,
            'weight' => 60,
            'joinDate' => Carbon::now(),
            'userId' => $user->id
        ]);

        $user2 = \App\Models\User::factory()->create([
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'email' => 'admin@example.com',
            'password' => bcrypt('admin12345'),
            'dob' => $faker->date,
            'gender' => 'male',
            'address' => $faker->address,
            'state' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'country' => 'Indonesia',
            'zipCode' => '80361',
            'phoneNumber' => $faker->phoneNumber,
            'status' => '1',
            'academyId' => 1,
        ]);
        $user2->assignRole($role2);
        $admin = Admin::factory()->create([
            'position' => 'staff',
            'userId' => $user2->id
        ]);

        $user3 = \App\Models\User::factory()->create([
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'email' => 'superadmin@example.com',
            'password' => bcrypt('superadmin12345'),
            'dob' => $faker->date,
            'gender' => 'male',
            'address' => $faker->address,
            'state' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'country' => 'Indonesia',
            'zipCode' => '80361',
            'phoneNumber' => $faker->phoneNumber,
            'status' => '1',
            'academyId' => 1,
        ]);
        $user3->assignRole($role3);

        $user4 = \App\Models\User::factory()->create([
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'email' => 'coach@example.com',
            'password' => bcrypt('coach12345'),
            'dob' => $faker->date,
            'gender' => 'male',
            'address' => $faker->address,
            'state' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'country' => 'Indonesia',
            'zipCode' => '80361',
            'phoneNumber' => $faker->phoneNumber,
            'status' => '1',
            'academyId' => 1,
        ]);
        $user4->assignRole($role4);
        $coach = Coach::factory()->create([
            'certificationLevel' => 'UEFA Pro',
            'specialization' => 'tactic',
            'height' => 175,
            'weight' => 60,
            'hireDate' => Carbon::now(),
            'userId' => $user->id
        ]);
    }
}
