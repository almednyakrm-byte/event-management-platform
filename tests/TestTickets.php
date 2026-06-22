<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controller\TicketsController;
use App\Repository\TicketsRepository;
use App\Entity\Tickets;
use App\Service\TicketsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestTickets extends TestCase
{
    private $controller;
    private $repository;
    private $service;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock('PDO');
        $this->repository = $this->createMock(TicketsRepository::class);
        $this->service = $this->createMock(TicketsService::class);
        $this->controller = new TicketsController($this->repository, $this->service);
    }

    public function testGetTickets()
    {
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                new Tickets(1, 'Ticket 1', 'Description 1'),
                new Tickets(2, 'Ticket 2', 'Description 2'),
            ]);

        $response = $this->controller->getTickets();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['tickets' => [
            ['id' => 1, 'title' => 'Ticket 1', 'description' => 'Description 1'],
            ['id' => 2, 'title' => 'Ticket 2', 'description' => 'Description 2'],
        ]], $response->getContent());
    }

    public function testGetTicket()
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new Tickets(1, 'Ticket 1', 'Description 1'));

        $response = $this->controller->getTicket(1);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['ticket' => ['id' => 1, 'title' => 'Ticket 1', 'description' => 'Description 1']], $response->getContent());
    }

    public function testGetTicketNotFound()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->controller->getTicket(1);
    }

    public function testCreateTicket()
    {
        $request = new Request();
        $request->request->set('title', 'Ticket 1');
        $request->request->set('description', 'Description 1');

        $this->service->expects($this->once())
            ->method('createTicket')
            ->with('Ticket 1', 'Description 1')
            ->willReturn(new Tickets(1, 'Ticket 1', 'Description 1'));

        $response = $this->controller->createTicket($request);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(['ticket' => ['id' => 1, 'title' => 'Ticket 1', 'description' => 'Description 1']], $response->getContent());
    }

    public function testUpdateTicket()
    {
        $request = new Request();
        $request->request->set('title', 'Ticket 1 Updated');
        $request->request->set('description', 'Description 1 Updated');

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new Tickets(1, 'Ticket 1', 'Description 1'));

        $this->service->expects($this->once())
            ->method('updateTicket')
            ->with(1, 'Ticket 1 Updated', 'Description 1 Updated')
            ->willReturn(new Tickets(1, 'Ticket 1 Updated', 'Description 1 Updated'));

        $response = $this->controller->updateTicket(1, $request);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['ticket' => ['id' => 1, 'title' => 'Ticket 1 Updated', 'description' => 'Description 1 Updated']], $response->getContent());
    }

    public function testUpdateTicketNotFound()
    {
        $request = new Request();
        $request->request->set('title', 'Ticket 1 Updated');
        $request->request->set('description', 'Description 1 Updated');

        $this->expectException(NotFoundHttpException::class);
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->controller->updateTicket(1, $request);
    }

    public function testDeleteTicket()
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new Tickets(1, 'Ticket 1', 'Description 1'));

        $this->service->expects($this->once())
            ->method('deleteTicket')
            ->with(1);

        $response = $this->controller->deleteTicket(1);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteTicketNotFound()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->controller->deleteTicket(1);
    }
}