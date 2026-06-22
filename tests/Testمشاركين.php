<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\ParticipansController;
use App\Repository\ParticipansRepository;
use App\Entity\Participans;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TestParticipans extends TestCase
{
    private $controller;
    private $repository;
    private $entityManager;
    private $router;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ParticipansRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->router = $this->createMock(RouterInterface::class);

        $this->controller = new ParticipansController($this->repository, $this->entityManager, $this->router);
    }

    public function testGetAllParticipans(): void
    {
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([new Participans()]);

        $response = $this->controller->getAllParticipans();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetParticipan(): void
    {
        $participan = new Participans();
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($participan);

        $response = $this->controller->getParticipan(1);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testCreateParticipan(): void
    {
        $request = new Request([], [], ['name' => 'John Doe']);
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->repository->expects($this->once())
                ->method('create')
                ->with('John Doe')
            );

        $response = $this->controller->createParticipan($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testUpdateParticipan(): void
    {
        $participan = new Participans();
        $request = new Request([], [], ['name' => 'Jane Doe']);
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($participan);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $response = $this->controller->updateParticipan(1, $request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testDeleteParticipan(): void
    {
        $participan = new Participans();
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($participan);
        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($participan);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $response = $this->controller->deleteParticipan(1);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}



// ParticipansController.php

namespace App\Controller;

use App\Repository\ParticipansRepository;
use App\Entity\Participans;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ParticipansController
{
    private $repository;
    private $entityManager;
    private $router;

    public function __construct(ParticipansRepository $repository, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function getAllParticipans(): Response
    {
        $participans = $this->repository->findAll();
        return new Response(json_encode($participans));
    }

    public function getParticipan(int $id): Response
    {
        $participan = $this->repository->find($id);
        return new Response(json_encode($participan));
    }

    public function createParticipan(Request $request): Response
    {
        $participan = $this->repository->create($request->get('name'));
        $this->entityManager->persist($participan);
        $this->entityManager->flush();
        return new Response('', Response::HTTP_CREATED);
    }

    public function updateParticipan(int $id, Request $request): Response
    {
        $participan = $this->repository->find($id);
        $participan->setName($request->get('name'));
        $this->entityManager->flush();
        return new Response('', Response::HTTP_OK);
    }

    public function deleteParticipan(int $id): Response
    {
        $participan = $this->repository->find($id);
        $this->entityManager->remove($participan);
        $this->entityManager->flush();
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}