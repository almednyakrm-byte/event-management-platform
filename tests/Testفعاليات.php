<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\FaaliyetController;
use App\Repository\FaaliyetRepository;
use App\Service\FaaliyetService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TestFaaliyet extends TestCase
{
    private $controller;
    private $repository;
    private $service;
    private $router;
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(\PDO::class);
        $this->repository = $this->createMock(FaaliyetRepository::class);
        $this->service = $this->createMock(FaaliyetService::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->controller = new faaliyetController($this->repository, $this->service, $this->router);
    }

    public function testGetFaaliyet()
    {
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Faaliyet 1'],
                ['id' => 2, 'name' => 'Faaliyet 2'],
            ]);

        $request = new Request();
        $response = $this->controller->getFaaliyet($request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testPostFaaliyet()
    {
        $this->service->expects($this->once())
            ->method('create')
            ->with(['name' => 'Faaliyet 1'])
            ->willReturn(['id' => 1, 'name' => 'Faaliyet 1']);

        $request = new Request([], [], ['name' => 'Faaliyet 1']);
        $response = $this->controller->postFaaliyet($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testPutFaaliyet()
    {
        $this->service->expects($this->once())
            ->method('update')
            ->with(1, ['name' => 'Faaliyet 1'])
            ->willReturn(['id' => 1, 'name' => 'Faaliyet 1']);

        $request = new Request([], [], ['name' => 'Faaliyet 1']);
        $response = $this->controller->putFaaliyet(1, $request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testDeleteFaaliyet()
    {
        $this->service->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $request = new Request();
        $response = $this->controller->deleteFaaliyet(1, $request);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}



// App\Controller\FaaliyetController.php

namespace App\Controller;

use App\Repository\FaaliyetRepository;
use App\Service\FaaliyetService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class FaaliyetController
{
    private $repository;
    private $service;
    private $router;

    public function __construct(FaaliyetRepository $repository, FaaliyetService $service, RouterInterface $router)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->router = $router;
    }

    public function getFaaliyet(Request $request)
    {
        $faaliyet = $this->repository->findAll();
        return new Response(json_encode($faaliyet), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    public function postFaaliyet(Request $request)
    {
        $faaliyet = $this->service->create($request->request->all());
        return new Response(json_encode($faaliyet), Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    public function putFaaliyet($id, Request $request)
    {
        $faaliyet = $this->service->update($id, $request->request->all());
        return new Response(json_encode($faaliyet), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    public function deleteFaaliyet($id, Request $request)
    {
        $this->service->delete($id);
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}