<?php

use Slim\Factory\AppFactory;

require '../vendor/autoload.php';

$container = require '../config/container.php';
$routing = require '../config/routing.php';

$app = AppFactory::createFromContainer($container);
$routing($app);
$app->run();
