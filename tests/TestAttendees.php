<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controller\AttendeesController;
use App\Repository\AttendeesRepository;
use App\Entity\Attendee;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ParameterCollection;

class TestAttendees extends TestCase
{
    private $attendeesController;
    private $attendeesRepository;
    private $entityManager;
    private $router;
    private $request;

    protected function setUp(): void
    {
        $this->attendeesRepository = $this->createMock(AttendeesRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->request = $this->createMock(Request::class);

        $this->attendeesController = new AttendeesController(
            $this->attendeesRepository,
            $this->entityManager,
            $this->router
        );
    }

    public function testGetAttendees(): void
    {
        $attendees = [
            new Attendee('John Doe', 'john@example.com'),
            new Attendee('Jane Doe', 'jane@example.com'),
        ];

        $this->attendeesRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($attendees);

        $response = $this->attendeesController->getAttendees($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testPostAttendee(): void
    {
        $attendee = new Attendee('John Doe', 'john@example.com');
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($attendee);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = $this->attendeesController->postAttendee($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testPutAttendee(): void
    {
        $attendee = new Attendee('John Doe', 'john@example.com');
        $this->entityManager
            ->expects($this->once())
            ->method('merge')
            ->with($attendee);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = $this->attendeesController->putAttendee($this->request, 1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDeleteAttendee(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($this->entityManager->find(Attendee::class, 1));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = $this->attendeesController->deleteAttendee($this->request, 1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}



// AttendeesController.php
namespace App\Controller;

use App\Repository\AttendeesRepository;
use App\Entity\Attendee;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;

class AttendeesController
{
    private $attendeesRepository;
    private $entityManager;
    private $router;

    public function __construct(
        AttendeesRepository $attendeesRepository,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ) {
        $this->attendeesRepository = $attendeesRepository;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function getAttendees(Request $request): JsonResponse
    {
        $attendees = $this->attendeesRepository->findAll();

        return new JsonResponse($attendees, Response::HTTP_OK);
    }

    public function postAttendee(Request $request): JsonResponse
    {
        $attendee = new Attendee($request->get('name'), $request->get('email'));
        $this->entityManager->persist($attendee);
        $this->entityManager->flush();

        return new JsonResponse($attendee, Response::HTTP_CREATED);
    }

    public function putAttendee(Request $request, int $id): JsonResponse
    {
        $attendee = $this->entityManager->find(Attendee::class, $id);
        $attendee->setName($request->get('name'));
        $attendee->setEmail($request->get('email'));
        $this->entityManager->merge($attendee);
        $this->entityManager->flush();

        return new JsonResponse($attendee, Response::HTTP_OK);
    }

    public function deleteAttendee(Request $request, int $id): JsonResponse
    {
        $attendee = $this->entityManager->find(Attendee::class, $id);
        $this->entityManager->remove($attendee);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}