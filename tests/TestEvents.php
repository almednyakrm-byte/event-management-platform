<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controller\EventsController;
use App\Repository\EventsRepository;
use App\Entity\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TestEvents extends TestCase
{
    private $eventsController;
    private $eventsRepository;
    private $entityManager;
    private $router;
    private $tokenStorage;

    protected function setUp(): void
    {
        $this->eventsRepository = $this->createMock(EventsRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);

        $this->eventsController = new EventsController(
            $this->eventsRepository,
            $this->entityManager,
            $this->router,
            $this->tokenStorage
        );
    }

    public function testGetEvents(): void
    {
        $events = [
            new Events(),
            new Events(),
            new Events(),
        ];

        $this->eventsRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($events);

        $response = $this->eventsController->getEvents();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testGetEvent(): void
    {
        $event = new Events();

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($event);

        $response = $this->eventsController->getEvent(1);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testGetEventNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->eventsController->getEvent(1);
    }

    public function testPostEvent(): void
    {
        $event = new Events();
        $event->setName('Test Event');
        $event->setDescription('Test Description');

        $this->eventsRepository->expects($this->once())
            ->method('save')
            ->with($event);

        $response = $this->eventsController->postEvent($event);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testPutEvent(): void
    {
        $event = new Events();
        $event->setName('Test Event');
        $event->setDescription('Test Description');

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($event);

        $this->eventsRepository->expects($this->once())
            ->method('save')
            ->with($event);

        $response = $this->eventsController->putEvent(1, $event);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testPutEventNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $event = new Events();
        $event->setName('Test Event');
        $event->setDescription('Test Description');

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->eventsController->putEvent(1, $event);
    }

    public function testDeleteEvent(): void
    {
        $event = new Events();

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($event);

        $this->eventsRepository->expects($this->once())
            ->method('remove')
            ->with($event);

        $response = $this->eventsController->deleteEvent(1);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteEventNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->eventsRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->eventsController->deleteEvent(1);
    }
}