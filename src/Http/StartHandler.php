<?php

namespace App\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Psr\Log\LoggerInterface;
use App\Service\Shopify;
use App\Repository\ShopRepository;
use App\Service\Session;

class StartHandler implements RequestHandlerInterface
{
    private $logger;
    private $repository;
    private $client;
    private $session;

    public function __construct(LoggerInterface $logger, Shopify $client, ShopRepository $repository, Session $session)
    {
        $this->logger = $logger;
        $this->client = $client;
        $this->session = $session;
        $this->repository = $repository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();

        $shop = $params['shop'];
        $response = new Response();
        $this->logger->critical($shop);
        $data = $this->repository->get($shop);
        if ($data) {
            
            if (!isset($_GET['session'])) {
                return $response->withHeader('Location', sprintf('https://%s/admin/apps/%s', $shop, $this->client->getApiKey()));
            }

            if ($this->client->verifyHmac($params)) {
                $this->session->set('shop', $shop);
                return $response->withHeader('Location', '/protected/dashboard');
            }            
        }

        return $response->withHeader('Location', $this->client->generateAuthUrl($shop));
    }
}
