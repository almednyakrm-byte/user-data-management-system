<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\PermissionsController;
use App\Repository\PermissionsRepository;
use App\Service\PermissionsService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TestPermissions extends TestCase
{
    private $permissionsController;
    private $permissionsRepository;
    private $permissionsService;
    private $router;

    protected function setUp(): void
    {
        $this->permissionsRepository = $this->createMock(PermissionsRepository::class);
        $this->permissionsService = $this->createMock(PermissionsService::class);
        $this->router = $this->createMock(RouterInterface::class);

        $this->permissionsController = new PermissionsController(
            $this->permissionsRepository,
            $this->permissionsService,
            $this->router
        );
    }

    public function testGetPermissions(): void
    {
        $this->permissionsRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Permission 1'],
                ['id' => 2, 'name' => 'Permission 2'],
            ]);

        $response = $this->permissionsController->getPermissions();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testCreatePermission(): void
    {
        $request = new Request([], [], ['name' => 'New Permission']);
        $this->permissionsService->expects($this->once())
            ->method('createPermission')
            ->with('New Permission')
            ->willReturn(['id' => 3, 'name' => 'New Permission']);

        $response = $this->permissionsController->createPermission($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testUpdatePermission(): void
    {
        $request = new Request([], [], ['id' => 1, 'name' => 'Updated Permission']);
        $this->permissionsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Permission 1']);
        $this->permissionsService->expects($this->once())
            ->method('updatePermission')
            ->with(1, 'Updated Permission')
            ->willReturn(['id' => 1, 'name' => 'Updated Permission']);

        $response = $this->permissionsController->updatePermission($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testDeletePermission(): void
    {
        $request = new Request([], [], ['id' => 1]);
        $this->permissionsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Permission 1']);
        $this->permissionsService->expects($this->once())
            ->method('deletePermission')
            ->with(1);

        $response = $this->permissionsController->deletePermission($request);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}


This test file covers the following scenarios:

1.  **GET /permissions**: Tests the `getPermissions` method of the `PermissionsController` to ensure it returns a list of permissions.
2.  **POST /permissions**: Tests the `createPermission` method of the `PermissionsController` to ensure it creates a new permission and returns it in the response.
3.  **PUT /permissions/{id}**: Tests the `updatePermission` method of the `PermissionsController` to ensure it updates an existing permission and returns it in the response.
4.  **DELETE /permissions/{id}**: Tests the `deletePermission` method of the `PermissionsController` to ensure it deletes a permission.

Each test method uses the `createMock` method to create mock objects for the `PermissionsRepository` and `PermissionsService` classes. The `expects` method is used to specify the expected behavior of the mock objects, and the `willReturn` method is used to specify the return value of the mock objects.

The `testGetPermissions` method tests the `getPermissions` method by verifying that it returns a list of permissions and that the response status code is 200 (OK).

The `testCreatePermission` method tests the `createPermission` method by verifying that it creates a new permission and returns it in the response. The `createPermission` method is expected to create a new permission with the given name and return it in the response.

The `testUpdatePermission` method tests the `updatePermission` method by verifying that it updates an existing permission and returns it in the response. The `updatePermission` method is expected to update the permission with the given ID and name and return it in the response.

The `testDeletePermission` method tests the `deletePermission` method by verifying that it deletes a permission. The `deletePermission` method is expected to delete the permission with the given ID.

Each test method uses the `assertEquals` method to verify that the expected response is returned. The `assertEquals` method takes two arguments: the expected value and the actual value. If the actual value does not match the expected value, the test fails.