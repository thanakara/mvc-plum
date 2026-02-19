<?php

declare(strict_types=1);

use App\App;
use App\Router;
use App\Config;
use App\Controllers\HomeController;

require_once __DIR__ . "/../vendor/autoload.php";
define("VIEWDIR", __DIR__ . "/../views");


$router = new Router;
$router->get(
    route: "/",
    // action: fn() => "<i>__index@home_controller</i>",
    action: [HomeController::class, "index"]
);

$request = [
    "uri"       =>  $_SERVER["REQUEST_URI"],
    "method"    =>  $_SERVER["REQUEST_METHOD"],
];

$config = new Config(env: $_ENV);

$app = new App($router, $request, $config);
$app->run();
