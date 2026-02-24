<?php

declare(strict_types=1);

use App\App;
use App\Router;
use App\Config;
use Dotenv\Dotenv;
use App\Models\ViewModel;
use App\Controllers\HomeController;
use App\Controllers\AccountsController;

require_once __DIR__ . "/../vendor/autoload.php";
define("VIEWDIR", __DIR__ . "/../views");

$dotenv = Dotenv::createImmutable(dirname(__DIR__, levels: 2));
$dotenv->load();

$router = new Router;
$router->registerFromControllerAttrs(
    controllers: [
        HomeController::class,
        AccountsController::class
    ]
);

$request = [
    "uri"     =>  $_SERVER["REQUEST_URI"],
    "method"  =>  $_SERVER["REQUEST_METHOD"],
];

$config = new Config(env: $_ENV);

$app = new App($router, $request, $config);
$app->run();

$conn = App::proxy();

$viewModel = new ViewModel();
$result = $viewModel->select(viewName: "active_users");

echo "<h3 style='text-align: center;background-color: lightgreen;'>SQLViewModel --active: </h3>";
echo "<pre>";
echo json_encode($result, JSON_PRETTY_PRINT);
echo "</pre>";
