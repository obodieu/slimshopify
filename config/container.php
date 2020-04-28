<?php

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use App\Service\Shopify;
use App\Service\Session;
use App\Service\View;

$dotenv = new Dotenv\Dotenv(__DIR__ . str_repeat(DIRECTORY_SEPARATOR . '..', 1));
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    'settings' => [

        'shopify' => [
            'key' => $_ENV['API_KEY'],
            'secret' => $_ENV['SECRET'],
            'scopes' => $_ENV['SCOPES'],  
            'host' => $_ENV['HOST'],
        ],       
        
        'db' => [
            'host' => $_ENV['DB_HOST'],
            'db' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'pass' => $_ENV['DB_PASS'],
        ],

        'displayErrorDetails' => true, // Should be set to false in production

        'loggers' => [
            [
                'name' => 'shopify-app',
                'path' => 'php://stderr',
                'level' => Logger::DEBUG,
            ],
            [
                'name' => 'shopify-app',
                'path' => __DIR__ . '/../var/log/app.log',
                'level' => Logger::DEBUG,
            ]
        ],
    ],
]);

$containerBuilder->addDefinitions([
    LoggerInterface::class => function (ContainerInterface $c) {
        $loggers = $c->get('settings')['loggers'];
        $logger = new Logger('app');
        foreach ($loggers as $settings) {
            $handler = new StreamHandler($settings['path'], $settings['level']);
            $logger->pushHandler($handler);
        }
 
        return $logger;
    },
]);

$containerBuilder->addDefinitions([PDO::class => function (ContainerInterface $c) {
    $settings = $c->get('settings')['db'];

    $charset = 'utf8';
    $collate = 'utf8_unicode_ci';
    $dsn = "mysql:host=${settings['host']};dbname=${settings['db']};charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset COLLATE $collate"
    ];

    return new PDO($dsn, $settings['user'], $settings['pass'], $options);
}]);

$containerBuilder->addDefinitions([
    Shopify::class => function (ContainerInterface $c) {
        
        $settings = $c->get('settings')['shopify'];
        $client = new Shopify($settings['key'], $settings['secret'], $settings['scopes'], $settings['host'] . '/shopify');

        return $client;
    },
]);

$containerBuilder->addDefinitions([
    Session::class => function (ContainerInterface $c) {
        return new Session();
    },
]);

$containerBuilder->addDefinitions([View::class => function() {
    return View::create(__DIR__ . '/../view',
        ['cache' => __DIR__ . '/../var/cache']);
}]);

return $containerBuilder->build();