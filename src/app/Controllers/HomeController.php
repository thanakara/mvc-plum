<?php

declare(strict_types=1);

namespace App\Controllers;

use App\TwigView;
use App\Attributes\Get;
use App\Models\ViewModel;

class HomeController
{
    public function __construct(private ViewModel $viewModel) {}

    #[Get("/")]
    public function index(): TwigView
    {
        return TwigView::make(
            view: "index",
            params: ["fromGet" => $_GET]
        );
    }

    #[Get("/active")]
    public function active(): TwigView
    {
        $activeUsers = $this->viewModel->select(viewName: "active_users");

        return TwigView::make(
            view: "active",
            params: [
                "activeUsers" => $activeUsers,
                "regionCount" => count(array_unique(array_column($activeUsers, "region"))),
                "accountCount" => count(array_unique(array_column($activeUsers, "account_name"))),
            ]
        );
    }
}
