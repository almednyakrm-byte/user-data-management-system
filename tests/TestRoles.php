<?php

namespace App\Tests\Controller;

use App\Controller\RolesController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;

class TestRoles extends TestCase
{
    private $rolesController;
    private $pdoMock;

    protected function setUp(): void
    {
        $this->rolesController = new RolesController();
        $this->pdoMock = $this->createMock(PDO::class);
    }

    public function testGetRoles()
    {
        $this->pdoMock->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM roles')
            ->willReturn($this->createMock(\PDOStatement::class));

        $response = $this->rolesController->getRoles($this->pdoMock);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreateRole()
    {
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO roles (name, description) VALUES (:name, :description)')
            ->willReturn($this->createMock(\PDOStatement::class));

        $response = $this->rolesController->createRole($this->pdoMock, 'Admin', 'Administrator');
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testUpdateRole()
    {
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('UPDATE roles SET name = :name, description = :description WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $response = $this->rolesController->updateRole($this->pdoMock, 1, 'Admin', 'Administrator');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteRole()
    {
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM roles WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $response = $this->rolesController->deleteRole($this->pdoMock, 1);
        $this->assertEquals(204, $response->getStatusCode());
    }
}



// RolesController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesController
{
    public function getRoles(PDO $pdo): Response
    {
        $stmt = $pdo->query('SELECT * FROM roles');
        $roles = $stmt->fetchAll();

        return new JsonResponse($roles);
    }

    public function createRole(PDO $pdo, string $name, string $description): Response
    {
        $stmt = $pdo->prepare('INSERT INTO roles (name, description) VALUES (:name, :description)');
        $stmt->execute(['name' => $name, 'description' => $description]);

        return new JsonResponse(['message' => 'Role created successfully'], 201);
    }

    public function updateRole(PDO $pdo, int $id, string $name, string $description): Response
    {
        $stmt = $pdo->prepare('UPDATE roles SET name = :name, description = :description WHERE id = :id');
        $stmt->execute(['name' => $name, 'description' => $description, 'id' => $id]);

        return new JsonResponse(['message' => 'Role updated successfully'], 200);
    }

    public function deleteRole(PDO $pdo, int $id): Response
    {
        $stmt = $pdo->prepare('DELETE FROM roles WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return new JsonResponse(['message' => 'Role deleted successfully'], 204);
    }
}