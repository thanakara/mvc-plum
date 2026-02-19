<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\RouteNotFoundException;

class App
{
    // private static Database $database;

    public function __construct(
        protected Router $router,
        protected array $request,
        protected Config $config,
    ) {
        // static::$database = new Database($config->db ?? [])
    }

    // public static function proxy(): Database
    // {
    //     return static::$database;
    // }

    public function run()
    {
        try {
            echo $this->router->resolve(
                requestUri: $this->request["uri"],
                requestMethod: $this->request["method"]
            );
        } catch (RouteNotFoundException) {
            http_response_code(404);
            echo View::make(view: "error/404");
        }
    }
}
