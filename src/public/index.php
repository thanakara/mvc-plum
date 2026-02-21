<?php

declare(strict_types=1);

use App\App;
use App\Router;
use App\Config;
use Dotenv\Dotenv;
use App\Models\UsersModel;
use App\Models\AccountsModel;
use App\Controllers\HomeController;
use App\Controllers\AccountsController;
use App\Models\ViewModel;

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
    "uri"       =>  $_SERVER["REQUEST_URI"],
    "method"    =>  $_SERVER["REQUEST_METHOD"],
];

$config = new Config(env: $_ENV);

$app = new App($router, $request, $config);
$app->run();


/**
 * TODO: ABC Model also initializes database in __construct()
 */
function transactionPDO(
    string $accountName,
    string $region,
    string $email,
    bool $isActive,
) {

    try {
        $pdoDB = App::proxy();
        $pdoDB->beginTransaction();

        $usersModel = new UsersModel();
        $accountsModel = new AccountsModel();

        $userId = $usersModel->create(email: $email, isActive: $isActive);
        $accountId = $accountsModel->create(
            userId: $userId,
            accountName: $accountName,
            region: $region
        );

        $pdoDB->commit();
    } catch (\Throwable) {
        // rollback when active transaction
        if ($pdoDB->inTransaction()) {
            $pdoDB->rollBack();
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    transactionPDO(
        accountName: $_POST["account_name"],
        region: $_POST["region"],
        email: $_POST["email"],
        isActive: true,
    );
    echo "_success_or_rollback" . "<br />";
} else {
    echo "_transactionPDO_not_triggered" . "<br />";
}
echo "<p style='background-color: lightgreen;text-align: center;'>" .
    "SQL View Model:</p> <pre>";
$viewModel = new ViewModel();
print_r($viewModel->select(viewName: "active_users"));
echo "</pre>";
