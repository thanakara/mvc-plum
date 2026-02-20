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
        return View::make(
            view: "accounts/index",
            params: ["fromPost" => $_POST]
        );
    }
}
