<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Attributes\Get;


class HealthController
{

    #[Get("/health")]
    public function index(): View
    {
        return View::make(view: "health/index", params: []);
    }
}
