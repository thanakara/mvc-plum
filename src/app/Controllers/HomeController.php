<?php

declare(strict_types=1);

namespace App\Controllers;

class HomeController
{
    // Not adding Attrs yet
    public function index()
    {
        echo <<<HTML
        <h3 style="text-align: center;background-color: lightgray">
            <i>__index@home_controller</i>
            </h3>
        HTML;
    }
}
