<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Attributes\Get;


class HomeController
{

    #[Get("/")]
    public function index(): View
    {
        return View::make(
            view: "index",
            params: ["fromGet" => $_GET]
        );
    }
}
