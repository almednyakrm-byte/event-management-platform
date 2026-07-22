<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\CommunicationsController;
use App\Repository\CommunicationsRepository;
use App\Service\CommunicationsService;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;

class TestCommunications extends TestCase
{
    private $controller;
    private $repository;
    private $service;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repository = $this->createMock(CommunicationsRepository::class);
        $this->service = $this->createMock(CommunicationsService::class);
        $this->controller = new CommunicationsController($this->repository, $this->service);
    }

    public function testGetCommunications()
    {
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Communication 1'],
                ['id' => 2, 'name' => 'Communication 2'],
            ]);

        $response = $this->controller->getCommunications();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            ['id' => 1, 'name' => 'Communication 1'],
            ['id' => 2, 'name' => 'Communication 2'],
        ], $response->toArray());
    }

    public function testCreateCommunication()
    {
        $data = ['name' => 'New Communication'];
        $this->service->expects($this->once())
            ->method('create')
            ->with($data)
            ->willReturn(['id' => 3, 'name' => 'New Communication']);

        $response = $this->controller->createCommunication($data);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(['id' => 3, 'name' => 'New Communication'], $response->toArray());
    }

    public function testUpdateCommunication()
    {
        $data = ['id' => 1, 'name' => 'Updated Communication'];
        $this->service->expects($this->once())
            ->method('update')
            ->with($data)
            ->willReturn(['id' => 1, 'name' => 'Updated Communication']);

        $response = $this->controller->updateCommunication($data);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['id' => 1, 'name' => 'Updated Communication'], $response->toArray());
    }

    public function testDeleteCommunication()
    {
        $id = 1;
        $this->service->expects($this->once())
            ->method('delete')
            ->with($id)
            ->willReturn(true);

        $response = $this->controller->deleteCommunication($id);
        $this->assertEquals(204, $response->getStatusCode());
    }
}


This test file covers the following scenarios:

- `testGetCommunications`: Tests the GET request to retrieve all communications.
- `testCreateCommunication`: Tests the POST request to create a new communication.
- `testUpdateCommunication`: Tests the PUT request to update an existing communication.
- `testDeleteCommunication`: Tests the DELETE request to delete a communication.

Each test method uses the `createMock` method to create a mock object for the `PDO`, `CommunicationsRepository`, and `CommunicationsService` classes. The `expects` method is used to define the expected behavior of the mock objects, and the `willReturn` method is used to specify the return value of the mock object.

The test methods also use the `assertEquals` method to verify that the response status code and data match the expected values.