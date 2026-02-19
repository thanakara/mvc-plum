<?php

declare(strict_types=1);

session_start();

class Controller
{
    /**
     * Get route
     */
    public function index()
    {
        echo <<<HTML
        <h3 style="text-align: center;background-color: lightgrey;">
            <i>@__index__</i>
            </h3>
        HTML;
    }
}


$controller = new Controller();
$controller->index();
