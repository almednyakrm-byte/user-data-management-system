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

        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['name' => $user['name']]);

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

        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['name' => $user['name'], 'id' => $user['id']]);

        $response = $this->usersController->updateUser($user);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($user), $response->getBody()->getContents());
    }

    public function testDeleteUser()
    {
        $user = ['id' => 1, 'name' => 'John Doe'];

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM users WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $this->pdo->expects($this->once())
            ->method('execute')
            ->with(['id' => $user['id']]);

        $response = $this->usersController->deleteUser($user['id']);

        $this->assertEquals(204, $response->getStatusCode());
    }
}