<?php

declare(strict_types=1);

use App\Exceptions\RouteNotFoundException;
use App\View;

require_once __DIR__ . "/../vendor/autoload.php";
define("VIEWDIR", __DIR__ . "/../views");

session_start();

$router = new App\Router();

$router->get(
    route: "/",
    // action: fn() => "<i>__index@home_controller</i>",
    action: [App\Controllers\HomeController::class, "index"]
);


try {
    echo $router->resolve(
        requestUri: $_SERVER["REQUEST_URI"],
        requestMethod: $_SERVER["REQUEST_METHOD"],
    );
} catch (RouteNotFoundException) {
    http_response_code(404);
    echo View::make(view: "error/404");
}
