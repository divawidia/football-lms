<?php

namespace Database\Seeders;

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
            'name' => 'player',
            'email' => 'player@example.com',
            'dob',
            'gender',
            'address',
            'state',
            'city',
            'country',
            'zipCode',
            'phoneNumber',
            'status',
            'academyId'
        ]);
        $user->assignRole($role1);

        $user = \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([
            'name' => 'Super-Admin',
            'email' => 'superadmin@example.com',
        ]);
        $user->assignRole($role3);

        $user = \App\Models\User::factory()->create([
            'name' => 'coach',
            'email' => 'coach@example.com',
        ]);
        $user->assignRole($role4);
    }
}
