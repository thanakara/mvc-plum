<?php

declare(strict_types=1);

namespace App;

use App\Databases\PDODatabase;
use App\Databases\DBALDatabase;
use App\Databases\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;
use App\Exceptions\RouteNotFoundException;

class App
{
    // private static PDODatabase $database;
    private static DBALDatabase $database;
    private static EntityManagerInterface $em;

    public function __construct(
        protected Router $router,
        protected array $request,
        protected Config $config,
    ) {
        // static::$database = new PDODatabase(config: $config->db ?? []);
        static::$database = new DBALDatabase(config: $config->db ?? []);
        static::$em = EntityManagerFactory::create(config: $config->db ?? []);
    }

    public static function proxy(): DBALDatabase | PDODatabase
    {
        return static::$database;
    }

    public static function emProxy(): EntityManagerInterface
    {
        return static::$em;
    }

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
