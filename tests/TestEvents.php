<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\EventsController;
use App\Repository\EventsRepository;
use App\Service\EventsService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestEvents extends TestCase
{
    private $eventsController;
    private $eventsRepository;
    private $eventsService;
    private $pdoMock;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->eventsRepository = $this->createMock(EventsRepository::class);
        $this->eventsService = $this->createMock(EventsService::class);
        $this->eventsController = new EventsController($this->eventsRepository, $this->eventsService);
    }

    public function testGetEvents(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Event 1'],
                ['id' => 2, 'name' => 'Event 2'],
            ]);

        $response = $this->eventsController->getEvents();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['events' => [
            ['id' => 1, 'name' => 'Event 1'],
            ['id' => 2, 'name' => 'Event 2'],
        ]], json_decode($response->getContent(), true));
    }

    public function testGetEvent(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Event 1']);

        $response = $this->eventsController->getEvent(1);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['event' => ['id' => 1, 'name' => 'Event 1']], json_decode($response->getContent(), true));
    }

    public function testGetEventNotFound(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->eventsController->getEvent(1);
    }

    public function testCreateEvent(): void
    {
        $this->eventsService->expects($this->once())
            ->method('createEvent')
            ->with(['name' => 'Event 1'])
            ->willReturn(['id' => 1, 'name' => 'Event 1']);

        $request = new Request([], [], ['name' => 'Event 1']);
        $response = $this->eventsController->createEvent($request);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(['event' => ['id' => 1, 'name' => 'Event 1']], json_decode($response->getContent(), true));
    }

    public function testUpdateEvent(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Event 1']);

        $this->eventsService->expects($this->once())
            ->method('updateEvent')
            ->with(1, ['name' => 'Event 2'])
            ->willReturn(['id' => 1, 'name' => 'Event 2']);

        $request = new Request([], [], ['name' => 'Event 2']);
        $response = $this->eventsController->updateEvent(1, $request);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['event' => ['id' => 1, 'name' => 'Event 2']], json_decode($response->getContent(), true));
    }

    public function testUpdateEventNotFound(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->eventsController->updateEvent(1, new Request());
    }

    public function testDeleteEvent(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Event 1']);

        $this->eventsService->expects($this->once())
            ->method('deleteEvent')
            ->with(1);

        $response = $this->eventsController->deleteEvent(1);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteEventNotFound(): void
    {
        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->eventsController->deleteEvent(1);
    }
}