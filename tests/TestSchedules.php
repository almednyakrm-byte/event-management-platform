<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\SchedulesController;
use App\Repository\SchedulesRepository;
use App\Entity\Schedule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\PDOTools;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestSchedules extends TestCase
{
    private $schedulesController;
    private $schedulesRepository;
    private $entityManager;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDOTools::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->schedulesRepository = $this->createMock(SchedulesRepository::class);
        $this->schedulesController = new SchedulesController($this->schedulesRepository, $this->entityManager);

        $this->entityManager->method('getRepository')->willReturn($this->schedulesRepository);
        $this->schedulesRepository->method('findAll')->willReturn([]);
        $this->schedulesRepository->method('find')->willReturn(null);
        $this->schedulesRepository->method('save')->willReturn(new Schedule());
        $this->schedulesRepository->method('remove')->willReturn(null);
    }

    public function testGetSchedules()
    {
        $response = $this->schedulesController->getSchedules();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetSchedule()
    {
        $schedule = new Schedule();
        $this->schedulesRepository->method('find')->willReturn($schedule);
        $response = $this->schedulesController->getSchedule(1);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetScheduleNotFound()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->schedulesController->getSchedule(1);
    }

    public function testCreateSchedule()
    {
        $request = new Request();
        $request->request->set('name', 'Test Schedule');
        $response = $this->schedulesController->createSchedule($request);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testUpdateSchedule()
    {
        $schedule = new Schedule();
        $this->schedulesRepository->method('find')->willReturn($schedule);
        $request = new Request();
        $request->request->set('name', 'Updated Test Schedule');
        $response = $this->schedulesController->updateSchedule(1, $request);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testUpdateScheduleNotFound()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->schedulesController->updateSchedule(1, new Request());
    }

    public function testDeleteSchedule()
    {
        $schedule = new Schedule();
        $this->schedulesRepository->method('find')->willReturn($schedule);
        $response = $this->schedulesController->deleteSchedule(1);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteScheduleNotFound()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->schedulesController->deleteSchedule(1);
    }
}


This test file covers the following scenarios:

- `testGetSchedules`: Verifies that the `getSchedules` method returns a successful response.
- `testGetSchedule`: Verifies that the `getSchedule` method returns a successful response when a schedule is found.
- `testGetScheduleNotFound`: Verifies that the `getSchedule` method throws a `NotFoundHttpException` when a schedule is not found.
- `testCreateSchedule`: Verifies that the `createSchedule` method returns a successful response.
- `testUpdateSchedule`: Verifies that the `updateSchedule` method returns a successful response when a schedule is found.
- `testUpdateScheduleNotFound`: Verifies that the `updateSchedule` method throws a `NotFoundHttpException` when a schedule is not found.
- `testDeleteSchedule`: Verifies that the `deleteSchedule` method returns a successful response when a schedule is found.
- `testDeleteScheduleNotFound`: Verifies that the `deleteSchedule` method throws a `NotFoundHttpException` when a schedule is not found.

Note that this test file uses a mock object for the `PDO` instance, and it assumes that the `SchedulesController` class is properly configured to use the `SchedulesRepository` and `EntityManager`.