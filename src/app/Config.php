<?php

declare(strict_types=1);

namespace App;

/**
 * @property-read ?array $repo
 * @property-read ?array $db
 */
class Config
{
    protected array $config = [];

    public function __construct(array $env)
    {
        $this->config = [
            "repo"  => "/mvc-plum",
            "db"    => [
                "driver"    =>  $env["DB_DRIVER"] ?? "pgsql", # --pdo
                // "driver"    =>  $env["DB_DRIVER"] ?? "pdo_pgsql", # --dbal
                "dbname"    =>  $env["DB_NAME"],
                "user"      =>  $env["DB_USER"],
                "host"      =>  $env["DB_HOST"],
                "password"  =>  $env["DB_PASSWORD"],
            ],
        ];
    }

    public function __get(string $name)
    {
        return $this->config[$name] ?? null;
    }
}
