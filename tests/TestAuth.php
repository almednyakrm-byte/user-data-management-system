<?php

namespace App\Tests\Unit\Auth;

use App\Auth\AuthService;
use App\Auth\AuthRepository;
use App\Auth\User;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TestAuth extends TestCase
{
    private $authService;
    private $authRepository;
    private $entityManager;
    private $tokenStorage;
    private $session;

    protected function setUp(): void
    {
        $this->authRepository = $this->createMock(AuthRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->session = $this->createMock(SessionInterface::class);

        $this->authService = new AuthService(
            $this->authRepository,
            $this->entityManager,
            $this->tokenStorage,
            $this->session
        );
    }

    public function testLogin()
    {
        $username = 'testuser';
        $password = 'testpassword';

        $this->authRepository->expects($this->once())
            ->method('findUserByUsername')
            ->with($username)
            ->willReturn(new User($username, $password));

        $token = $this->createMock(TokenInterface::class);
        $this->tokenStorage->expects($this->once())
            ->method('setToken')
            ->with($token);

        $this->authService->login($username, $password);

        $this->assertTrue($this->tokenStorage->isAuthenticated());
    }

    public function testRegister()
    {
        $username = 'testuser';
        $password = 'testpassword';

        $this->authRepository->expects($this->once())
            ->method('findUserByUsername')
            ->with($username)
            ->willReturn(null);

        $this->authRepository->expects($this->once())
            ->method('saveUser')
            ->with(new User($username, $password));

        $this->authService->register($username, $password);

        $this->assertTrue($this->authRepository->isUserSaved());
    }

    public function testLoginWithInvalidCredentials()
    {
        $username = 'testuser';
        $password = 'testpassword';

        $this->authRepository->expects($this->once())
            ->method('findUserByUsername')
            ->with($username)
            ->willReturn(null);

        $this->authService->login($username, $password);

        $this->assertFalse($this->tokenStorage->isAuthenticated());
    }

    public function testRegisterWithExistingUsername()
    {
        $username = 'testuser';
        $password = 'testpassword';

        $this->authRepository->expects($this->once())
            ->method('findUserByUsername')
            ->with($username)
            ->willReturn(new User($username, $password));

        $this->authService->register($username, $password);

        $this->assertFalse($this->authRepository->isUserSaved());
    }
}


This test file covers the following scenarios:

- `testLogin`: Tests that a user can log in successfully with valid credentials.
- `testRegister`: Tests that a user can register successfully with valid credentials.
- `testLoginWithInvalidCredentials`: Tests that a user cannot log in with invalid credentials.
- `testRegisterWithExistingUsername`: Tests that a user cannot register with an existing username.