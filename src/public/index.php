<?php

declare(strict_types=1);

use App\App;
use App\Router;
use App\Config;
use Dotenv\Dotenv;
use App\Container;
use App\Controllers\HomeController;
use App\Controllers\HealthController;
use App\Controllers\AccountsController;
use App\Databases\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;

require_once __DIR__ . "/../../vendor/autoload.php";
define("VIEWDIR", __DIR__ . "/../views");

$dotenv = Dotenv::createImmutable(dirname(__DIR__, levels: 2));
$dotenv->load();

/*
Config instantiated BEFORE the container,
so EntityManagerInterface binding can close over it cleanly.
The Container can't resolve EntityManagerInterface on its own since
it's an interface - it needs an explicit binding, which we provide here.
*/
$config = new Config(env: $_ENV);

$container = new Container();
$container->bind(
    id: EntityManagerInterface::class,
    concrete: fn() => EntityManagerFactory::create(config: $config->db ?? [])
);

$router = new Router(container: $container);
$router->registerFromControllerAttrs(
    controllers: [
        HomeController::class,
        AccountsController::class,
        HealthController::class,
    ]
);

$request = [
    "uri"     =>  $_SERVER["REQUEST_URI"],
    "method"  =>  $_SERVER["REQUEST_METHOD"],
];

$app = new App($router, $request, $config);
$app->run();
