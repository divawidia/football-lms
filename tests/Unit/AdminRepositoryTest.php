<?php


use App\Helpers\DatatablesHelper;
use App\Models\Academy;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\AdminManagements\AdminAccountCreatedDeleted;
use App\Notifications\AdminManagements\AdminAccountUpdated;
use App\Repository\AdminRepository;
use App\Repository\Interface\AdminRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\UserRepository;
use App\Services\AdminService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AdminRepository $adminRepository;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRepository = new AdminRepository(new Admin());
        $this->userRepository = new UserRepository(new User());

        Role::firstOrCreate(['name' => 'admin']);

        $this->user1 = User::factory()->create();
        $this->user1->assignRole('admin');
        $this->admin1 = Admin::factory()->create(['userId' => $this->user1->id]);

        $this->user2 = User::factory()->create();
        $this->user2->assignRole('admin');
        $this->admin2 = Admin::factory()->create(['userId' => $this->user2->id]);
    }

    /** @test */
    public function it_can_get_all_admins()
    {
        $admins = $this->adminRepository->getAll();

        $this->assertCount(2, $admins);
    }

    /** @test */
    public function it_can_find_an_admin_by_id()
    {
        $foundAdmin = $this->adminRepository->find($this->admin1->id);

        $this->assertEquals($this->admin1->id, $foundAdmin->id);
        $this->assertEquals($this->admin1->position, $foundAdmin->position);
    }

    /** @test */
    public function it_can_create_an_admin()
    {
        $userData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'dob' => fake()->date,
            'gender' => 'male',
            'address' => fake()->address,
            'state_id' => 1,
            'city_id' => 1,
            'country_id' => 1,
            'zipCode' => fake()->postcode,
            'phoneNumber' => fake()->phoneNumber,
            'status' => 1,
            'academyId' => Academy::factory()->create()->id,
        ];

        $adminData = [
            'position' => 'Test Admin',
            'hireDate' => now(),
        ];

        $user = $this->userRepository->createUserWithRole($userData, 'admin');
        $adminData['userId'] = $user->id;

        $admin = $this->adminRepository->create($adminData);

        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'position' => 'Test Admin',
            'userId' => $user->id
        ]);
    }

    /** @test */
    public function it_can_update_an_admin()
    {
        $updateData = [
            'position' => 'Updated Position',
            'firstName' => 'Updated First Name',
            'lastName' => 'Updated Last Name'
        ];

        $this->adminRepository->update($updateData, $this->admin1);

        $this->user1->refresh();
        $this->admin1->refresh();

        $this->assertEquals('Updated Position', $this->admin1->position);
        $this->assertEquals('Updated First Name', $this->user1->firstName);
        $this->assertEquals('Updated Last Name', $this->user1->lastName);
    }
}
