<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\View;
use App\Attributes\Get;
use App\Attributes\Post;
use App\Services\ORMAccountService;
// use App\Services\DBALAccountService;
// use App\Services\PDOAccountService;

class AccountsController
{
    // TODO: need DI Container since Router can handle args in constructor

    public function __construct(
        // private PDOAccountService $accountServicePDO = new PDOAccountService()
        // private DBALAccountService $accountServiceDBAL = new DBALAccountService()
        private ORMAccountService $ormAccountService
    ) {}

    #[Get("/accounts")]
    public function index(): View
    {
        return View::make(
            view: "accounts/index",
            params: ["fromGet" => $_GET]
        );
    }

    #[Get("/accounts/create")]
    public function create(): View
    {
        return View::make(
            view: "accounts/create",
            params: ["fromGet" => $_GET]
        );
    }

    #[Post("/accounts")]
    public function store(): mixed
    {
        $comesAsJson = str_contains($_SERVER["CONTENT_TYPE"] ?? "", "application/json");

        $data = $comesAsJson
            ? json_decode(file_get_contents("php://input"), true)
            : $_POST;

        if ($comesAsJson) {
            header("Content-Type: application/json");
        }

        try {
            $this->ormAccountService->createAccountWithUser(
                email: $data["email"],
                isActive: true,
                accountName: $data["account_name"],
                region: $data["region"],
            );

            $response = ["status" => "__success__"];
            http_response_code(201);
        } catch (\Throwable $e) {
            $response = [
                "status"  => "__error__",
                "message" => $e->getMessage(),
            ];
            http_response_code(500);
        }

        if ($comesAsJson) {
            echo json_encode($response, JSON_PRETTY_PRINT);
            return null;
        }

        return View::make(
            view: "accounts/index",
            params: [
                "fromGet"  => $_GET,
                "fromPost" => $_POST,
            ]
        );
    }
}
