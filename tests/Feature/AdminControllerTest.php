<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles are created
        Role::firstOrCreate(['name' => 'Super-Admin']);
        Role::firstOrCreate(['name' => 'admin']);

        // Create a super admin for authentication
        $this->superAdminUser = User::factory()->create();
        $this->superAdminUser->assignRole('Super-Admin');
        $this->superAdmin = Admin::factory()->create(['userId' => $this->superAdminUser->id]);
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** @test */
    public function authenticated_super_admin_can_view_admin_index()
    {
        $this->actingAs($this->superAdminUser);

        $response = $this->get(route('admin-managements.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.managements.admins.index');
    }

    /** @test */
    public function super_admin_can_create_new_admin()
    {
        Storage::fake('public/assets/user-profile/');

        $this->actingAs($this->superAdminUser);

        $userData = User::factory()->make(

        )->toArray();
        $adminData = Admin::factory()->make()->toArray();
        $data = array_merge($userData, $adminData);
        $data['password'] = 'Password123!';
        $data['password_confirmation'] = 'Password123!';
        $data['foto'] = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post(route('admin-managements.store'), $data);

        $response->assertRedirect(route('admin-managements.index'));
        $this->assertDatabaseHas('users', [
            'firstName' => $userData['firstName'],
            'lastName' => $userData['lastName'],
            'email' => $userData['email'],
        ]);
        $this->assertDatabaseHas('admins', [
            'position' => $adminData['position'],
        ]);
    }

    /** @test */
    public function super_admin_can_update_admin()
    {
        $this->actingAs($this->superAdminUser);

        $updateData = [
            'email' => $this->superAdminUser->email,
            'gender' => $this->superAdminUser->gender,
            'dob' => $this->superAdminUser->dob,
            'address' => $this->superAdminUser->address,
            'zipCode' => $this->superAdminUser->zipCode,
            'country_id' => $this->superAdminUser->country_id,
            'state_id' => $this->superAdminUser->state_id,
            'city_id' => $this->superAdminUser->city_id,
            'hireDate' => $this->superAdmin->hireDate,
            'firstName' => 'Updated',
            'lastName' => 'Name',
            'position' => 'Updated Position',
            'phoneNumber' => '9876543210',
        ];

        $response = $this->put(route('admin-managements.update', $this->superAdmin), $updateData);

        $response->assertRedirect(route('admin-managements.index'));

        $this->superAdmin->refresh();
        $this->superAdminUser->refresh();

        $this->assertEquals('Updated', $this->superAdminUser->firstName);
        $this->assertEquals('Name', $this->superAdminUser->lastName);
        $this->assertEquals('Updated Position', $this->superAdmin->position);
    }

    /** @test */
    public function super_admin_can_deactivate_admin()
    {
        $this->actingAs($this->superAdminUser);

        // Test deactivation
        $deactivateResponse = $this->patch(route('deactivate-admin', $this->superAdmin), ['status' => 0]);
        $deactivateResponse->assertStatus(200);

        $this->superAdminUser->refresh();
        $this->assertEquals('0', $this->superAdminUser->status);
    }

    public function super_admin_can_activate_admin()
    {
        $this->actingAs($this->superAdminUser);
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        $admin = Admin::factory()->create(['userId' => $adminUser->id]);

        // Test activation
        $activateResponse = $this->patch(route('activate-admin', $admin), ['status' => 1]);
        $activateResponse->assertStatus(200);

        $adminUser->refresh();
        $this->assertEquals('1', $adminUser->status);
    }

    /** @test */
    public function super_admin_can_delete_admin()
    {
        $this->actingAs($this->superAdminUser);

        $response = $this->delete(route('admin-managements.destroy', $this->superAdmin));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $this->superAdminUser->id]);
        $this->assertDatabaseMissing('admins', ['id' => $this->superAdmin->id]);
    }
}
