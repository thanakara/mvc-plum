<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Attributes\Get;
use App\Models\ViewModel;

class HomeController
{
    public function __construct(private ViewModel $viewModel) {}

    #[Get("/")]
    public function index(): View
    {
        return View::make(
            view: "index",
            params: ["fromGet" => $_GET]
        );
    }

    #[Get("/active")]
    public function active(): View
    {
        $activeUsers = $this->viewModel->select(viewName: "active_users");

        return View::make(
            view: "active",
            params: ["activeUsers" => $activeUsers]
        );
    }
}
