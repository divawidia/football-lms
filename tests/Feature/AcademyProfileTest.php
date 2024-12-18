<?php


use App\Models\Academy;
use App\Models\Admin;
use App\Models\Coach;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AcademyProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles are created
        Role::firstOrCreate(['name' => 'Super-Admin']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'coach']);
        Role::firstOrCreate(['name' => 'player']);

        // Create a super admin for authentication
        $this->superAdminUser = User::factory()->has(Admin::factory(), 'admin')->create();
        $this->superAdminUser->assignRole('Super-Admin');

        $this->coachUser = User::factory()->has(Coach::factory(), 'coach')->create();
        $this->coachUser->assignRole('coach');

        $this->playerUser = User::factory()->has(Player::factory(), 'player')->create();
        $this->playerUser->assignRole('player');

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->academy = Academy::factory()->create();
    }

    /** @test */
    public function test_authenticated_admin_can_view_academy_profile_page()
    {
        $this->actingAs($this->superAdminUser);

        $response = $this->get(route('edit-academy.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.academy-profile.edit');
        $response->assertSeeText('Edit Academy Profile');
    }

    public function test_authenticated_coach_cannot_view_academy_profile_page()
    {
        $this->actingAs($this->coachUser);

        $response = $this->get(route('edit-academy.edit'));

        $response->assertForbidden();
    }

    public function test_authenticated_player_cannot_view_academy_profile_page()
    {
        $this->actingAs($this->playerUser);

        $response = $this->get(route('edit-academy.edit'));

        $response->assertForbidden();
    }

    /** @test */
    public function test_admin_can_update_academy_profile()
    {
        Storage::fake('public/assets/user-profile/');

        $this->actingAs($this->superAdminUser);

        $data = Academy::factory()->make()->toArray();
        $data['logo'] = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->put(route('edit-academy.update'), $data);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('academies', [
            'academyName' => $data['academyName'],
            'phoneNumber' => $data['phoneNumber'],
            'email' => $data['email'],
        ]);
    }

    public function test_invalid_data_fails_validation()
    {
        $this->actingAs($this->superAdminUser);

        $invalidData = [
            'academyName' => '', // Missing name
            'email' => 'not-an-email', // Invalid email
            'address' => '', // Missing address
            'phoneNumber' => '', // Missing phone
            'zipCode' => 'not-a-number', // Non-numeric zip code
            'directorName' => '', // Missing director name
            'academyDescription' => '', // Missing description
            'logo' => UploadedFile::fake()->image('logo.jpg')->size(2048), // File too large
            'country_id' => 9999, // Non-existent ID
            'state_id' => 9999, // Non-existent ID
            'city_id' => 9999, // Non-existent ID
        ];

        $response = $this->put(route('edit-academy.update'), $invalidData);
        $response->assertSessionHasErrors([
            'academyName',
            'email',
            'address',
            'phoneNumber',
            'zipCode',
            'directorName',
            'academyDescription',
            'logo',
            'country_id',
            'state_id',
            'city_id',
        ]);
    }

    /** @test */
    public function test_coach_cannot_update_academy_profile()
    {
        Storage::fake('public/assets/user-profile/');

        $this->actingAs($this->coachUser);

        $data = Academy::factory()->make()->toArray();
        $data['logo'] = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->put(route('edit-academy.update'), $data);

        $response->assertForbidden();
    }
}
