<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

$router = new App\Router();

$router->get(
    route: "/",
    // action: fn() => "<i>__index@home_controller</i>",
    action: [App\Controllers\HomeController::class, "index"]
);

echo $router->resolve(
    requestUri: $_SERVER["REQUEST_URI"],
    requestMethod: $_SERVER["REQUEST_METHOD"],
);
