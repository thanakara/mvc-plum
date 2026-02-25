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
    private ORMAccountService $ormAccountService;

    public function __construct(
        // private PDOAccountService $accountServicePDO = new PDOAccountService()
        // private DBALAccountService $accountServiceDBAL = new DBALAccountService()

    )
    {
        $this->ormAccountService = new ORMAccountService(em: App::emProxy());
    }

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
    public function store()
    {
        $comesAsJson = str_contains($_SERVER["CONTENT_TYPE"] ?? "", "application/json");

        if ($comesAsJson) {
            header("Content-Type: application/json");
            $data = json_decode(file_get_contents("php://input"), true);

            try {
                $this->ormAccountService->createAccountWithUser(
                    accountName: $data["account_name"],
                    region: $data["region"],
                    email: $data["email"],
                    isActive: true,
                );
                echo json_encode(
                    ["status" => "__success__"],
                    JSON_PRETTY_PRINT
                );
            } catch (\Throwable $e) {
                http_response_code(500);
                echo json_encode(
                    [
                        "status" => "__error__",
                        "message" => $e->getMessage()
                    ],
                    JSON_PRETTY_PRINT
                );
            }
            return;
        }

        try {
            $this->ormAccountService->createAccountWithUser(
                accountName: $_POST["account_name"],
                region: $_POST["region"],
                email: $_POST["email"],
                isActive: true,
            );
            echo json_encode(
                ["status" => "__success__"],
                JSON_PRETTY_PRINT
            );
        } catch (\Throwable $e) {
            echo "<pre>";
            echo json_encode(
                [
                    "status" => "__error__",
                    "message" => $e->getMessage(),
                ],
                JSON_PRETTY_PRINT,
            );
            echo "</pre>";
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
