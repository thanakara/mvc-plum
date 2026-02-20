<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;

class AccountsController
{
    public function index(): View
    {
        return View::make(
            view: "accounts/index",
            params: ["fromGet" => $_GET]
        );
    }

    public function create(): View
    {
        return View::make(
            view: "accounts/create",
            params: ["fromGet" => $_GET]
        );
    }

    public function store()
    {
        /*
        The form is sending application/x-www-form-urlencoded 
        (or multipart/form-data), not JSON — so php://input gets
        either an empty string or the raw form-encoded body,
        and json_decode returns null. We handle both cases,
        or read from $_POST when it's a form submission:
        */
        $comesAsJson = str_contains($_SERVER["CONTENT_TYPE"] ?? "", "application/json");

        /*
        NOTE: using "CONTENT_TYPE" says:
        if you're sending json, I want json back.
        To fullfil this, curl must use -H "Content-Type: application/json"
        Using "HTTP_ACCEPT" says:
        i want you to return json; and must be specified in curl
        through -H "Accept: application/json"
        */
        if ($comesAsJson) {
            // header tells the client: whatever I send back will be json.
            header("Content-Type: application/json");

            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode($data, JSON_PRETTY_PRINT);
            return;
        }

        // else render View
        return View::make(
            view: "accounts/index",
            params: [
                "fromGet"  => $_GET,
                "fromPost" => $_POST,
            ]
        );
    }
}
