<?php

declare(strict_types=1);

namespace App;

use App\Databases\PDODatabase;
use App\Databases\DBALDatabase;
use App\Databases\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;
use App\Exceptions\RouteNotFoundException;
use App\Contracts\EmailValidationInterface;
// use App\Services\Emailable\GuzzleEmailValidationService as EmailableEmailValidationService;
use App\Services\AbstractApi\GuzzleEmailValidationService as AbstractEmailValidationService;


class App
{
    // private static PDODatabase $database;
    private static DBALDatabase $database;

    public function __construct(
        protected Container $container,
        protected Router $router,
        protected array $request,
        protected Config $config,
    ) {
        // static::$database = new PDODatabase(config: $config->db ?? []);
        static::$database = new DBALDatabase(config: $config->db ?? []);

        $container->bind(
            id: EntityManagerInterface::class,
            concrete: fn() => EntityManagerFactory::create(config: $config->db ?? [])
        );
        $container->bind(
            id: EmailValidationInterface::class,
            concrete: fn() => new AbstractEmailValidationService(
                apiKey: $config->apiKeys["abstract"]
            )
        );
        /* TODO: Fallback when the binding or Service fails
        /──────────────────────────────────────────────────────────────/
        $container->bind(
            id: EmailValidationInterface::class,
            concrete: fn() => new EmailableEmailValidationService(
                apiKey: $config->apiKeys["emailable"]
            )
        );        
        /──────────────────────────────────────────────────────────────/
        */
    }

    public static function proxy(): DBALDatabase | PDODatabase
    {
        return static::$database;
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
            echo TwigView::make(
                view: "error/404.html",
                params: ["path" => $_SERVER["REQUEST_URI"]]
            );
        }
    }
}
