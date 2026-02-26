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

require_once __DIR__ . "/../vendor/autoload.php";
define("VIEWDIR", __DIR__ . "/../views");

$dotenv = Dotenv::createImmutable(dirname(__DIR__, levels: 2));
$dotenv->load();

$container = new Container;

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

$config = new Config(env: $_ENV);

$app = new App($router, $request, $config);
$app->run();
