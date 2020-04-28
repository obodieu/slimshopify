<?php

namespace App\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use App\Service\Shopify;
use App\Repository\ShopRepository;
use App\Service\Session;
use App\Service\View;

class DashboardHandler implements RequestHandlerInterface
{
    private $view;
    private $client;
    private $repository;
    private $session;

    public function __construct(View $view, Shopify $client, ShopRepository $repository, Session $session)
    {
        $this->view = $view;
        $this->client = $client;
        $this->repository = $repository;
        $this->session = $session;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        return $this->view->render($response, 'dashboard.twig', ['shop' => $this->session->get('shop')]);
    }
}