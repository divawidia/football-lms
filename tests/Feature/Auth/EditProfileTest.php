<?php

namespace Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles are created
        Role::firstOrCreate(['name' => 'Super-Admin']);
        Role::firstOrCreate(['name' => 'admin']);

        // Create a super admin for authentication
        $this->user = User::factory()->create();
        $this->user->assignRole('Super-Admin');

        $this->actingAs($this->user);
    }
    public function test_edit_profile_page_can_be_rendered(): void
    {
        $response = $this->get(route('edit-account.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.user-profile.edit');
    }

    public function test_user_can_update_account_profile()
    {
        Storage::fake('images');

        $data = [
            'firstName' => 'Updated firstName',
            'lastName' => 'Updated firstName',
            'email' => 'updatedemail@example.com',
            'gender' => 'female',
            'dob' => '1987-07-06',
            'address' => 'Updated address',
            'phoneNumber' => '(+62) 980 0475 198',
            'zipCode' => '80361',
            'foto' => UploadedFile::fake()->image('image.jpg'),
            'country_id' => $this->user->country_id,
            'state_id' => $this->user->state_id,
            'city_id' => $this->user->city_id,
        ];

        $response = $this->put(route('edit-account.update'), $data);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'firstName' => 'Updated firstName',
            'lastName' => 'Updated firstName',
            'email' => 'updatedemail@example.com',
            'gender' => 'female',
            'dob' => '1987-07-06',
        ]);
    }
    public function test_user_update_account_profile_validation(): void
    {
        Storage::fake('images');

        $invalidData = [
            'firstName' => '',
            'lastName' => '',
            'email' => 'wrong email format',
            'gender' => 'other',
            'dob' => '2025-07-06',
            'address' => '',
            'phoneNumber' => '',
            'zipCode' => 'wrong zipcode',
            'foto' => UploadedFile::fake()->image('logo.jpg')->size(2048), // File too large
            'country_id' => 9999, // Non-existent ID
            'state_id' => 9999, // Non-existent ID
            'city_id' => 9999, // Non-existent ID
        ];

        $response = $this->put(route('edit-account.update'), $invalidData);

        $response->assertSessionHasErrors([
            'firstName',
            'lastName',
            'email',
            'foto',
            'dob',
            'gender',
            'address',
            'state_id',
            'city_id',
            'country_id',
            'zipCode',
            'phoneNumber',
        ]);
    }

    public function test_change_password_page_can_be_rendered(): void
    {
        $response = $this->get(route('reset-password.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.user-profile.reset-password');
    }

    public function test_user_can_change_password_account()
    {
        $data = [
            'old_password' => $this->user->password,
            'password' => 'Password@!23',
            'password_confirmation' =>'Password@!23'
        ];

        $response = $this->put(route('reset-password.update'), $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');
    }

    public function test_change_password_validation()
    {
        $data = [
            'old_password' => 'some password',
            'password' => 'Password',
            'password_confirmation' =>'Password'
        ];

        $response = $this->put(route('reset-password.update'), $data);

        $response->assertSessionHasErrors([
            'password',
        ]);
    }

    public function test_old_password_validation()
    {
        $data = [
            'old_password' => 'some password',
            'password' => 'Password@123',
            'password_confirmation' =>'Password@123'
        ];

        $response = $this->put(route('reset-password.update'), $data);

        $response->assertSessionHas('error', 'Current password is incorrect.');
    }
}
