<?php

use App\Http\StartHandler;
use App\Http\DashboardHandler;
use App\Http\ShopifyPostInstallHandler;
use Psr\Log\LoggerInterface;    

use Slim\Views\TwigMiddleware;

use App\Middleware\SessionMiddleware;
use App\Middleware\AuthMiddleware;

use Slim\Routing\RouteCollectorProxy;

return function($app) {

    // $app->add(TwigMiddleware::createFromContainer($app));
    $app->add(SessionMiddleware::class);
    $app->addErrorMiddleware(getenv('APP_DEBUG') == 'true', true, true, $app->getContainer()->get(LoggerInterface::class));

    $app->get('/', StartHandler::class);
    $app->get('/shopify', ShopifyPostInstallHandler::class);

    $app
        ->group('/protected', function (RouteCollectorProxy $group) {
            $group->get('/dashboard', DashboardHandler::class);
        })
        ->add(AuthMiddleware::class);

};