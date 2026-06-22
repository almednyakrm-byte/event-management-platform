<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\PaymentsController;
use App\Repository\PaymentsRepository;
use App\Service\PaymentsService;
use PHPUnit\Framework\MockObject\MockObject;
use PDO;

class TestPayments extends TestCase
{
    private $controller;
    private $repository;
    private $service;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repository = $this->createMock(PaymentsRepository::class);
        $this->service = $this->createMock(PaymentsService::class);
        $this->controller = new PaymentsController($this->repository, $this->service);
    }

    public function testGetPayments()
    {
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                ['id' => 1, 'amount' => 100],
                ['id' => 2, 'amount' => 200],
            ]);

        $response = $this->controller->getPayments();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            ['id' => 1, 'amount' => 100],
            ['id' => 2, 'amount' => 200],
        ], json_decode($response->getBody()->getContents(), true));
    }

    public function testCreatePayment()
    {
        $this->service->expects($this->once())
            ->method('createPayment')
            ->with(['amount' => 500])
            ->willReturn(['id' => 3, 'amount' => 500]);

        $response = $this->controller->createPayment(['amount' => 500]);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(['id' => 3, 'amount' => 500], json_decode($response->getBody()->getContents(), true));
    }

    public function testUpdatePayment()
    {
        $this->service->expects($this->once())
            ->method('updatePayment')
            ->with(1, ['amount' => 600])
            ->willReturn(['id' => 1, 'amount' => 600]);

        $response = $this->controller->updatePayment(1, ['amount' => 600]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['id' => 1, 'amount' => 600], json_decode($response->getBody()->getContents(), true));
    }

    public function testDeletePayment()
    {
        $this->service->expects($this->once())
            ->method('deletePayment')
            ->with(1)
            ->willReturn(true);

        $response = $this->controller->deletePayment(1);
        $this->assertEquals(204, $response->getStatusCode());
    }
}



// PaymentsController.php
namespace App\Controller;

use App\Repository\PaymentsRepository;
use App\Service\PaymentsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentsController
{
    private $repository;
    private $service;

    public function __construct(PaymentsRepository $repository, PaymentsService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getPayments(Request $request)
    {
        $payments = $this->repository->findAll();
        return new JsonResponse($payments, 200);
    }

    public function createPayment(Request $request)
    {
        $payment = $this->service->createPayment($request->request->all());
        return new JsonResponse($payment, 201);
    }

    public function updatePayment(Request $request, int $id)
    {
        $payment = $this->service->updatePayment($id, $request->request->all());
        return new JsonResponse($payment, 200);
    }

    public function deletePayment(Request $request, int $id)
    {
        $this->service->deletePayment($id);
        return new Response('', 204);
    }
}



// PaymentsService.php
namespace App\Service;

use App\Repository\PaymentsRepository;

class PaymentsService
{
    private $repository;

    public function __construct(PaymentsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createPayment(array $data)
    {
        // Create a new payment
        // ...
        return ['id' => 1, 'amount' => 500];
    }

    public function updatePayment(int $id, array $data)
    {
        // Update a payment
        // ...
        return ['id' => 1, 'amount' => 600];
    }

    public function deletePayment(int $id)
    {
        // Delete a payment
        // ...
        return true;
    }
}



// PaymentsRepository.php
namespace App\Repository;

class PaymentsRepository
{
    public function findAll()
    {
        // Return all payments
        // ...
        return [
            ['id' => 1, 'amount' => 100],
            ['id' => 2, 'amount' => 200],
        ];
    }
}