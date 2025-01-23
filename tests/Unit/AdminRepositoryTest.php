<?php


use App\Models\Academy;
use App\Models\Admin;
use App\Models\User;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminRepositoryTest extends TestCase
{
    protected $adminMock;
    protected $adminRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Admin model
        $this->adminMock = Mockery::mock(Admin::class);

        // Create an instance of the repository with the mocked model
        $this->adminRepository = new AdminRepository($this->adminMock);
    }

    protected function tearDown(): void
    {
        // Clean up Mockery
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_with_default_parameters()
    {
        // Mock the query builder and expectations
        $queryMock = Mockery::mock(Builder::class);
        $queryMock->shouldReceive('with')->with(['user'])->andReturnSelf();
        $queryMock->shouldReceive('get')->with(['*'])->andReturn(collect(['admin1', 'admin2']));

        $this->adminMock->shouldReceive('with')->with(['user'])->andReturn($queryMock);

        // Call the method and assert the result
        $result = $this->adminRepository->getAll();
        $this->assertEquals(collect(['admin1', 'admin2']), $result);
    }

    public function test_get_all_with_count_retrieval_method()
    {
        // Mock the query builder and expectations
        $queryMock = Mockery::mock(Builder::class);
        $queryMock->shouldReceive('with')->with(['user'])->andReturnSelf();
        $queryMock->shouldReceive('count')->andReturn(5);

        $this->adminMock->shouldReceive('with')->with(['user'])->andReturn($queryMock);

        // Call the method and assert the result
        $result = $this->adminRepository->getAll(['user'], false, 'count');
        $this->assertEquals(5, $result);
    }

    public function test_get_all_with_single_retrieval_method()
    {
        // Mock the query builder and expectations
        $queryMock = Mockery::mock(Builder::class);
        $queryMock->shouldReceive('with')->with(['user'])->andReturnSelf();
        $queryMock->shouldReceive('first')->with(['*'])->andReturn((object) ['id' => 1, 'name' => 'Admin']);

        $this->adminMock->shouldReceive('with')->with(['user'])->andReturn($queryMock);

        // Call the method and assert the result
        $result = $this->adminRepository->getAll(['user'], false, 'single');
        $this->assertEquals((object) ['id' => 1, 'name' => 'Admin'], $result);
    }

    public function test_find_admin_by_id()
    {
        // Mock the findOrFail method
        $this->adminMock->shouldReceive('findOrFail')
            ->with(1)
            ->andReturn((object) ['id' => 1, 'name' => 'Admin']);

        // Call the method and assert the result
        $result = $this->adminRepository->find(1);
        $this->assertEquals((object) ['id' => 1, 'name' => 'Admin'], $result);
    }

    public function test_create_admin()
    {
        // Mock the create method
        $data = ['name' => 'Admin', 'email' => 'admin@example.com'];
        $this->adminMock->shouldReceive('create')
            ->with($data)
            ->andReturn((object) ['id' => 1, 'name' => 'Admin', 'email' => 'admin@example.com']);

        // Call the method and assert the result
        $result = $this->adminRepository->create($data);
        $this->assertEquals((object) ['id' => 1, 'name' => 'Admin', 'email' => 'admin@example.com'], $result);
    }

    public function test_update_admin()
    {
        // Mock the Admin model and its relationships
        $adminMock = Mockery::mock(Admin::class);
        $userMock = Mockery::mock();

        $data = ['name' => 'Updated Admin', 'email' => 'updated@example.com'];

        // Expectations for the update method
        $adminMock->shouldReceive('update')->with($data)->andReturn(true);
        $adminMock->shouldReceive('getAttribute')->with('user')->andReturn($userMock);
        $userMock->shouldReceive('update')->with($data)->andReturn(true);

        // Call the method and assert the result
        $result = $this->adminRepository->update($data, $adminMock);
        $this->assertEquals(1, $result);
    }
}
