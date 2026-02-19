<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;

class HomeController
{
    /*
    Redirects to view and extracts get params from the title.
    example: localhost:8000/?name=al&email=a@b.com
    */
    public function index(): View
    {
        return View::make(
            view: "index",
            params: ["fromGet" => $_GET]
        );
    }
}
