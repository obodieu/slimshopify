<?php

namespace App\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Psr\Log\LoggerInterface;
use App\Service\Shopify;
use App\Repository\ShopRepository;

class ShopifyPostInstallHandler implements RequestHandlerInterface
{
    private $logger;
    private $client;
    private $repository;

    public function __construct(LoggerInterface $logger, Shopify $client, ShopRepository $repository)
    {
        $this->logger = $logger;
        $this->client = $client;
        $this->repository = $repository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $shop = $request->getQueryParams()['shop'];
        $token = $this->client->getAccessToken($shop);

        $this->repository->insert([
            'shop' => $shop,
            'token' => $token
        ]);
        
        $response = new Response();
        return $response->withHeader('Location', sprintf('https://%s/admin/apps/%s', $shop, $this->client->getApiKey()));
    }
}