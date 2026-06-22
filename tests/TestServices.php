<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Services\Services;

class TestServices extends TestCase
{
    private $services;

    protected function setUp(): void
    {
        $this->services = new Services();
    }

    public function testGetServices()
    {
        $pdo = $this->createMock(\PDO::class);
        $stmt = $this->createMock(\PDOStatement::class);

        $pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM services')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with([]);

        $stmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Service 1'],
                ['id' => 2, 'name' => 'Service 2'],
            ]);

        $this->services->setPdo($pdo);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $result = $this->services->getServices($request, $response);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetServiceById()
    {
        $pdo = $this->createMock(\PDO::class);
        $stmt = $this->createMock(\PDOStatement::class);

        $pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM services WHERE id = :id')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with([':id' => 1]);

        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1, 'name' => 'Service 1']);

        $this->services->setPdo($pdo);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $response = $this->createMock(ResponseInterface::class);

        $result = $this->services->getServiceById($request, $response);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testCreateService()
    {
        $pdo = $this->createMock(\PDO::class);
        $stmt = $this->createMock(\PDOStatement::class);

        $pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO services (name) VALUES (:name)')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with([':name' => 'New Service']);

        $stmt->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $this->services->setPdo($pdo);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'New Service']);

        $response = $this->createMock(ResponseInterface::class);

        $result = $this->services->createService($request, $response);

        $this->assertIsArray($result);
        $this->assertEquals(201, $result['status']);
    }

    public function testUpdateService()
    {
        $pdo = $this->createMock(\PDO::class);
        $stmt = $this->createMock(\PDOStatement::class);

        $pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE services SET name = :name WHERE id = :id')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with([':id' => 1, ':name' => 'Updated Service']);

        $stmt->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $this->services->setPdo($pdo);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['name' => 'Updated Service']);

        $response = $this->createMock(ResponseInterface::class);

        $result = $this->services->updateService($request, $response);

        $this->assertIsArray($result);
        $this->assertEquals(200, $result['status']);
    }

    public function testDeleteService()
    {
        $pdo = $this->createMock(\PDO::class);
        $stmt = $this->createMock(\PDOStatement::class);

        $pdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM services WHERE id = :id')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with([':id' => 1]);

        $stmt->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $this->services->setPdo($pdo);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn(1);

        $response = $this->createMock(ResponseInterface::class);

        $result = $this->services->deleteService($request, $response);

        $this->assertIsArray($result);
        $this->assertEquals(204, $result['status']);
    }
}