<?php

namespace App\Tests\Controller;

use App\Controller\TicketsController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;

class Testتذاكر extends TestCase
{
    private $controller;
    private $pdoMock;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->controller = new TicketsController($this->pdoMock);
    }

    public function testGetTickets()
    {
        $expectedResponse = ['tickets' => []];
        $this->pdoMock->expects($this->once())
            ->method('query')
            ->with('SELECT * FROM tickets')
            ->willReturn($this->createMock(\PDOStatement::class));

        $response = $this->controller->getTickets();
        $this->assertEquals($expectedResponse, $response);
    }

    public function testCreateTicket()
    {
        $ticketData = ['title' => 'Test Ticket', 'description' => 'This is a test ticket'];
        $expectedResponse = ['message' => 'Ticket created successfully'];

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO tickets (title, description) VALUES (:title, :description)')
            ->willReturn($this->createMock(\PDOStatement::class));

        $response = $this->controller->createTicket($ticketData);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testUpdateTicket()
    {
        $ticketId = 1;
        $ticketData = ['title' => 'Updated Ticket', 'description' => 'This is an updated ticket'];
        $expectedResponse = ['message' => 'Ticket updated successfully'];

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('UPDATE tickets SET title = :title, description = :description WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $response = $this->controller->updateTicket($ticketId, $ticketData);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testDeleteTicket()
    {
        $ticketId = 1;
        $expectedResponse = ['message' => 'Ticket deleted successfully'];

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM tickets WHERE id = :id')
            ->willReturn($this->createMock(\PDOStatement::class));

        $response = $this->controller->deleteTicket($ticketId);
        $this->assertEquals($expectedResponse, $response);
    }
}