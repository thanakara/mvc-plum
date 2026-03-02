<?php

declare(strict_types=1);

require_once __DIR__ . "/../../vendor/autoload.php";
define("VIEWDIR", __DIR__ . "/../views");
define("TEMPLATESDIR", dirname(VIEWDIR) . "/templates");

use App\App;
use App\Router;
use App\Config;
use Dotenv\Dotenv;
use App\Container;
use App\Controllers\HomeController;
use App\Controllers\CurlController;
use App\Controllers\HealthController;
use App\Controllers\AccountsController;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, levels: 2));
$dotenv->load();

/*
Config instantiated BEFORE the container,
so interfaces binding can close over it cleanly.
The Container can't resolve on its own since it's interfaces
- it needs an explicit binding, which is provided in App's contructor:
*/
$config = new Config(env: $_ENV);

$container = new Container();

$router = new Router(container: $container);
$router->registerFromControllerAttrs(
    controllers: [
        HomeController::class,
        AccountsController::class,
        HealthController::class,
        CurlController::class,
    ]
);

$request = [
    "uri"     =>  $_SERVER["REQUEST_URI"],
    "method"  =>  $_SERVER["REQUEST_METHOD"],
];

$app = new App($container, $router, $request, $config);
$app->run();
