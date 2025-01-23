<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Notifications\AdminManagement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdminUser;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdminUser = User::factory()->has(Admin::factory())->create();
        $this->superAdminUser->assignRole('Super-Admin');

        $this->adminUser = User::factory()->has(Admin::factory())->create();
        $this->adminUser->assignRole('admin');
    }

    /** @test */
    public function test_authenticated_super_admin_can_view_admin_index()
    {
        $this->actingAs($this->superAdminUser);

        $response = $this->get(route('admin-managements.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.managements.admins.index');
    }

    public function test_authenticated_admin_can_view_admin_index()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin-managements.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.managements.admins.index');
    }

    public function test_authenticated_super_admin_can_view_create_admin_page()
    {
        $this->actingAs($this->superAdminUser);

        $response = $this->get(route('admin-managements.create'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.managements.admins.create');
    }

    public function test_authenticated_admin_cannot_view_create_admin_page()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin-managements.create'));
        $response->assertStatus(403);
        $response->assertForbidden();
    }

    /** @test */
    public function test_super_admin_can_store_new_admin()
    {
        Notification::fake();
        Storage::fake('public/assets/user-profile/');

        $this->actingAs($this->superAdminUser);

        $data = User::factory()->make()->toArray();
        $data['password'] = 'Password123!';
        $data['password_confirmation'] = 'Password123!';
        $data['position'] = 'Super Admin';
        $data['hireDate'] = fake()->date;
        $data['foto'] = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post(route('admin-managements.store'), $data);

        $response->assertRedirect(route('admin-managements.index'));
        $this->assertDatabaseHas('users', [
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
        ]);
        $this->assertDatabaseHas('admins', [
            'position' => $data['position'],
        ]);
        Notification::assertSentTo($this->superAdminUser, AdminManagement::class);
    }

    /** @test */
    public function test_super_admin_can_update_admin()
    {
        $this->actingAs($this->superAdminUser);

        $updateData = [
            'email' => 'test@example.com',
            'gender' => 'male',
            'dob' => fake()->date,
            'address' => 'test address',
            'zipCode' => '80361',
            'country_id' => $this->adminUser->country_id,
            'state_id' => $this->adminUser->state_id,
            'city_id' => $this->adminUser->city_id,
            'hireDate' => fake()->date,
            'firstName' => 'updated name',
            'lastName' => 'updated name',
            'position' => 'Updated Position',
            'phoneNumber' => '9876543210',
        ];

        $response = $this->put(route('admin-managements.update', $this->adminUser->admin), $updateData);

        $response->assertRedirect(route('admin-managements.index'));
        $this->adminUser->refresh();
        $this->assertDatabaseHas('users', [
            'firstName' => $updateData['firstName'],
            'lastName' => $updateData['lastName'],
            'email' => $updateData['email'],
            'dob' => $updateData['dob'],
            'gender' => $updateData['gender'],
            'address' => $updateData['address'],
            'state_id' => $updateData['state_id'],
            'city_id' => $updateData['city_id'],
            'country_id' => $updateData['country_id'],
            'zipCode' => $updateData['zipCode'],
            'phoneNumber' => $updateData['phoneNumber'],
        ]);
        $this->assertDatabaseHas('admins', [
            'position' => $updateData['position'],
            'hireDate' => $updateData['hireDate'],
        ]);
    }

    /** @test */
    public function test_super_admin_can_deactivate_admin()
    {
        $this->actingAs($this->superAdminUser);

        $deactivateResponse = $this->patch(route('admin-managements.deactivate', $this->adminUser->admin));

        $deactivateResponse->assertStatus(200);
        $this->adminUser->refresh();
        $this->assertDatabaseHas('users', [
            'status' => '0',
        ]);
    }

    public function test_super_admin_can_activate_admin()
    {
        $this->actingAs($this->superAdminUser);

        $activateResponse = $this->patch(route('activate-admin', $this->adminUser->admin));

        $activateResponse->assertStatus(200);
        $this->adminUser->refresh();
        $this->assertDatabaseHas('users', [
            'status' => '1',
        ]);
    }

    /** @test */
    public function test_super_admin_can_delete_admin()
    {
        $this->actingAs($this->superAdminUser);

        $response = $this->delete(route('admin-managements.destroy', $this->adminUser->admin));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $this->adminUser->id]);
        $this->assertDatabaseMissing('admins', ['id' => $this->adminUser->admin->id]);
    }
}
