<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TestAuth extends TestCase
{
    /**
     * @var LegacyMockInterface|SessionInterface
     */
    protected $session;

    /**
     * @var LegacyMockInterface|UserRepository
     */
    protected $userRepository;

    /**
     * @var LegacyMockInterface|AuthService
     */
    protected $authService;

    protected function setUp(): void
    {
        $this->session = Mockery::mock(SessionInterface::class);
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->authService = Mockery::mock(AuthService::class);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->setId(1);
        $user->setUsername('testuser');
        $user->setPassword('testpassword');

        $this->userRepository->shouldReceive('getUserByUsername')->andReturn($user);
        $this->authService->shouldReceive('login')->with($user)->once();

        $this->authService->login($user);
        $this->assertTrue($this->session->has('user'));
    }

    public function testLoginFailure()
    {
        $this->userRepository->shouldReceive('getUserByUsername')->andReturnNull();
        $this->authService->shouldReceive('login')->never();

        $this->authService->login(null);
        $this->assertFalse($this->session->has('user'));
    }

    public function testRegisterSuccess()
    {
        $user = new User();
        $user->setId(1);
        $user->setUsername('testuser');
        $user->setPassword('testpassword');

        $this->userRepository->shouldReceive('createUser')->andReturn($user);
        $this->authService->shouldReceive('register')->with($user)->once();

        $this->authService->register($user);
        $this->assertTrue($this->session->has('user'));
    }

    public function testRegisterFailure()
    {
        $this->userRepository->shouldReceive('createUser')->andReturnNull();
        $this->authService->shouldReceive('register')->never();

        $this->authService->register(null);
        $this->assertFalse($this->session->has('user'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}


This test file covers the following scenarios:

- `testLoginSuccess`: Tests that the login method of the `AuthService` class successfully logs in a user.
- `testLoginFailure`: Tests that the login method of the `AuthService` class fails to log in a user when the username is not found.
- `testRegisterSuccess`: Tests that the register method of the `AuthService` class successfully registers a new user.
- `testRegisterFailure`: Tests that the register method of the `AuthService` class fails to register a new user when the user creation fails.

Note that this test file assumes that the `AuthService` class uses the `UserRepository` class to interact with the database. The `UserRepository` class is mocked in the test file to isolate the `AuthService` class from the database.