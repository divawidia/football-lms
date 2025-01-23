<?php

namespace Tests\Unit;

use App\Repository\Interface\AdminRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use Tests\TestCase;
use App\Services\AdminService;
use App\Helpers\DatatablesHelper;
use Mockery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;

class AdminServiceTest extends TestCase
{
    protected $adminRepositoryMock;
    protected $userRepositoryMock;
    protected $datatablesHelperMock;
    protected $adminService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->adminRepositoryMock = Mockery::mock(AdminRepositoryInterface::class);
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->datatablesHelperMock = Mockery::mock(DatatablesHelper::class);

        // Create an instance of AdminService with mocked dependencies
        $this->adminService = new AdminService(
            $this->adminRepositoryMock,
            $this->userRepositoryMock,
            $this->datatablesHelperMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_returns_json_response()
    {
        // Mock the admin repository to return a collection
        $admins = collect([
            (object) [
                'user' => (object) [
                    'id' => 1,
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'status' => '1',
                    'foto' => 'profile.jpg',
                    'dob' => '1990-01-01',
                    'email' => 'john@example.com',
                    'phoneNumber' => '1234567890',
                    'gender' => 'male'
                ],
                'hash' => 'abc123'
            ]
        ]);

        $this->adminRepositoryMock->shouldReceive('getAll')
            ->with(['user:id,firstName,lastName,status,foto,dob,email,phoneNumber,gender'])
            ->andReturn($admins);

        // Mock Datatables methods
        $this->datatablesHelperMock->shouldReceive('dropdown')->andReturn('<div>Dropdown</div>');
        $this->datatablesHelperMock->shouldReceive('linkDropdownItem')->andReturn('<a>Link</a>');
        $this->datatablesHelperMock->shouldReceive('buttonDropdownItem')->andReturn('<button>Button</button>');
        $this->datatablesHelperMock->shouldReceive('name')->andReturn('<div>Name</div>');
        $this->datatablesHelperMock->shouldReceive('activeNonactiveStatus')->andReturn('<span>Active</span>');

        // Call the index method
        $response = $this->adminService->index();

        // Assert that the response is a JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_count_all_admin()
    {
        // Mock the admin repository to return a count
        $this->adminRepositoryMock->shouldReceive('getAll')
            ->with([], false, 'count')
            ->andReturn(10);

        // Call the method and assert the result
        $result = $this->adminService->countAllAdmin();
        $this->assertEquals(10, $result);
    }

    public function test_count_new_admin_this_month()
    {
        // Mock the admin repository to return a count for this month
        $this->adminRepositoryMock->shouldReceive('getAll')
            ->with([], true, 'count')
            ->andReturn(5);

        // Call the method and assert the result
        $result = $this->adminService->countNewAdminThisMonth();
        $this->assertEquals(5, $result);
    }

    public function test_store_admin()
    {
        // Mock data
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'foto' => 'profile.jpg'
        ];

        // Mock the user repository to return a user
        $user = (object) ['id' => 1];
        $this->userRepositoryMock->shouldReceive('createUserWithRole')
            ->with($data, 'admin')
            ->andReturn($user);

        // Mock the admin repository to return an admin
        $admin = (object) ['id' => 1];
        $this->adminRepositoryMock->shouldReceive('create')
            ->with(array_merge($data, ['userId' => 1, 'status' => '1', 'academyId' => 1]))
            ->andReturn($admin);

        // Mock Notification
        Notification::fake();

        // Call the method and assert the result
        $loggedAdmin = (object) ['id' => 2];
        $result = $this->adminService->store($data, 1, $loggedAdmin);
        $this->assertEquals($admin, $result);

        // Assert that the notification was sent
        Notification::assertSentTo(
            [$this->userRepositoryMock->getAllAdminUsers()],
            \App\Notifications\AdminManagement::class
        );
    }

    public function test_update_admin()
    {
        // Mock data
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'foto' => 'new_profile.jpg'
        ];

        // Mock the admin repository to update the admin
        $admin = (object) ['id' => 1, 'user' => (object) ['foto' => 'old_profile.jpg']];
        $this->adminRepositoryMock->shouldReceive('update')
            ->with($data, $admin)
            ->andReturn(1);

        // Mock Notification
        Notification::fake();

        // Call the method and assert the result
        $loggedAdmin = (object) ['id' => 2];
        $result = $this->adminService->update($data, $admin, $loggedAdmin);
        $this->assertEquals($admin, $result);

        // Assert that the notification was sent
        Notification::assertSentTo(
            [$this->userRepositoryMock->getAllAdminUsers()],
            \App\Notifications\AdminManagement::class
        );
    }

    public function test_set_status()
    {
        // Mock the user repository to update the status
        $admin = (object) ['id' => 1];
        $this->userRepositoryMock->shouldReceive('updateUserStatus')
            ->with($admin, '1')
            ->andReturn(true);

        // Mock Notification
        Notification::fake();

        // Call the method and assert the result
        $loggedAdmin = (object) ['id' => 2];
        $result = $this->adminService->setStatus($admin, '1', $loggedAdmin);
        $this->assertTrue($result);

        // Assert that the notification was sent
        Notification::assertSentTo(
            [$this->userRepositoryMock->getAllAdminUsers()],
            \App\Notifications\AdminManagement::class
        );
    }

    public function test_destroy_admin()
    {
        // Mock the user repository to delete the admin
        $admin = (object) ['id' => 1, 'user' => (object) ['foto' => 'profile.jpg']];
        $this->userRepositoryMock->shouldReceive('delete')
            ->with($admin)
            ->andReturn(true);

        // Mock Notification
        Notification::fake();

        // Call the method and assert the result
        $loggedAdmin = (object) ['id' => 2];
        $result = $this->adminService->destroy($admin, $loggedAdmin);
        $this->assertTrue($result);

        // Assert that the notification was sent
        Notification::assertSentTo(
            [$this->userRepositoryMock->getAllAdminUsers()],
            \App\Notifications\AdminManagement::class
        );
    }
}
