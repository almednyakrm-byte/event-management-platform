<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\UsersController;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;

class TestUsers extends TestCase
{
    private $usersController;
    private $userRepository;
    private $userService;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userService = $this->createMock(UserService::class);
        $this->usersController = new UsersController($this->userRepository, $this->userService, $this->pdo);
    }

    public function testGetUsers()
    {
        $users = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
        ];

        $this->pdo->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM users')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->userRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($users);

        $response = $this->usersController->getUsers();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($users), $response->getBody()->getContents());
    }

    public function testCreateUser()
    {
        $user = ['id' => 1, 'name' => 'John Doe'];

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO users (name) VALUES (:name)')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->userRepository->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn($user);

        $response = $this->usersController->createUser($user);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(json_encode($user), $response->getBody()->getContents());
    }

    public function testUpdateUser()
    {
        $user = ['id' => 1, 'name' => 'John Doe'];

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE users SET name = :name WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->userRepository->expects($this->once())
            ->method('update')
            ->with($user)
            ->willReturn($user);

        $response = $this->usersController->updateUser($user);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($user), $response->getBody()->getContents());
    }

    public function testDeleteUser()
    {
        $id = 1;

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM users WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->userRepository->expects($this->once())
            ->method('delete')
            ->with($id)
            ->willReturn(true);

        $response = $this->usersController->deleteUser($id);

        $this->assertEquals(204, $response->getStatusCode());
    }
}



// App\Controller\UsersController.php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\UserService;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController
{
    private $userRepository;
    private $userService;
    private $pdo;

    public function __construct(UserRepository $userRepository, UserService $userService, PDO $pdo)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->pdo = $pdo;
    }

    public function getUsers(): Response
    {
        $users = $this->userRepository->findAll();
        return new JsonResponse($users);
    }

    public function createUser(Request $request): Response
    {
        $user = $request->request->all();
        $this->pdo->prepare('INSERT INTO users (name) VALUES (:name)')->execute($user);
        return new JsonResponse($user, 201);
    }

    public function updateUser(Request $request, int $id): Response
    {
        $user = $request->request->all();
        $this->pdo->prepare('UPDATE users SET name = :name WHERE id = :id')->execute($user);
        return new JsonResponse($user);
    }

    public function deleteUser(int $id): Response
    {
        $this->pdo->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => $id]);
        return new Response('', 204);
    }
}