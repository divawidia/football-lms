<?php


use App\Helpers\DatatablesHelper;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\AdminManagements\AdminAccountCreatedDeleted;
use App\Notifications\AdminManagements\AdminAccountUpdated;
use App\Repository\Interface\AdminRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use App\Services\AdminService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminServiceTest extends TestCase
{
    use RefreshDatabase;
    protected AdminService $adminService;
    private AdminRepositoryInterface $adminRepositoryMock;
    private UserRepositoryInterface $userRepositoryMock;
    private User|Collection|Model $loggedUser;
    protected function setUp(): void
    {
        parent::setUp();
        $this->loggedUser = User::factory()->create();
        $this->adminRepositoryMock = Mockery::mock(AdminRepositoryInterface::class);
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $datatablesHelper = Mockery::mock(DatatablesHelper::class);
        $this->adminService = new AdminService(
            $this->adminRepositoryMock,
            $this->userRepositoryMock,
            $this->loggedUser,
            $datatablesHelper
        );

        $this->mockUser = User::factory()->create();
        $this->mockAdmin = Admin::factory()->create(['userId' => $this->mockUser->id]);
    }
    public function test_can_store_admin()
    {
        Notification::fake();

        // Prepare test data
        $academyId = 1;
        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        // Create a mock user
        $mockUser = User::factory()->create([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);

        // Expectations
        $this->userRepositoryMock
            ->shouldReceive('createUserWithRole')
            ->once()
            ->with(
                Mockery::subset([
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'status' => '1',
                    'academyId' => $academyId
                ]),
                'admin'
            )
            ->andReturn($mockUser);

        $mockAdmin = new Admin([
            'userId' => $mockUser->id,
            'position' => $data['position'] ?? null
        ]);

        $this->adminRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::subset(['userId' => $mockUser->id]))
            ->andReturn($mockAdmin);

        $this->userRepositoryMock
            ->shouldReceive('getAllAdminUsers')
            ->once()
            ->andReturn(collect([$this->loggedUser]));

        // Execute the method
        $result = $this->adminService->store($data, $academyId);

        // Assertions
        $this->assertInstanceOf(Admin::class, $result);
        $this->assertEquals($mockUser->id, $result->userId);

        Notification::assertSentTo($this->loggedUser, AdminAccountCreatedDeleted::class);
    }

    /** @test */
    public function it_can_update_existing_admin()
    {
        Notification::fake();

        // Prepare update data
        $data = [
            'firstName' => 'Updated',
            'lastName' => 'Name',
            'position' => 'New Position'
        ];

        // Expectations
        $this->adminRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with(
                Mockery::subset([
                    'firstName' => 'Updated',
                    'lastName' => 'Name',
                    'position' => 'New Position'
                ]),
                $this->mockAdmin
            )
            ->andReturn(true);

        // Execute the method
        $result = $this->adminService->update($data, $this->mockAdmin);

        // Assertions
        $this->assertInstanceOf(Admin::class, $result);

        // Verify notification was sent
        Notification::assertSentTo($this->mockAdmin->user, AdminAccountUpdated::class);
    }

    /** @test */
    public function it_can_set_admin_status()
    {
        Notification::fake();

        // Expectations
        $this->userRepositoryMock
            ->shouldReceive('updateUserStatus')
            ->once()
            ->with($this->mockAdmin, '0')
            ->andReturn(true);

        // Execute the method
        $result = $this->adminService->setStatus($this->mockAdmin, '0');

        // Assertions
        $this->assertTrue($result);

        // Verify notification was sent
        Notification::assertSentTo($this->mockAdmin->user, AdminAccountUpdated::class);
    }

    /** @test */
    public function it_can_change_admin_password()
    {
        Notification::fake();

        // Prepare password change data
        $data = [
            'password' => 'newpassword123'
        ];

        // Expectations
        $this->userRepositoryMock
            ->shouldReceive('changePassword')
            ->once()
            ->with($data, $this->mockAdmin)
            ->andReturn(true);

        // Execute the method
        $result = $this->adminService->changePassword($data, $this->mockAdmin);

        // Assertions
        $this->assertTrue($result);

        // Verify notification was sent
        Notification::assertSentTo($this->mockAdmin->user, AdminAccountUpdated::class);
    }

    /** @test */
    public function it_can_destroy_admin()
    {
        Notification::fake();

        // Expectations for user repository delete
        $this->userRepositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($this->mockAdmin)
            ->andReturn(true);

        // Expectations for getting all admin users for notification
        $this->userRepositoryMock
            ->shouldReceive('getAllAdminUsers')
            ->once()
            ->andReturn(collect([$this->loggedUser]));

        // Execute the method
        $result = $this->adminService->destroy($this->mockAdmin);

        // Assertions
        $this->assertTrue($result);

        Notification::assertSentTo($this->loggedUser, AdminAccountCreatedDeleted::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
