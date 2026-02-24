<?php

declare(strict_types=1);

namespace App\Databases;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

/**
 * @mixin Connection
 */
class DBALDatabase
{
    private Connection $conn;

    public function __construct(array $config)
    {
        $this->conn = DriverManager::getConnection($config);
    }

    /**
     * This method proxies the call to the Connection object
     */
    public function __call(string $name, array $args)
    {
        return call_user_func_array(
            callback: [$this->conn, $name],
            args: $args,
        );
    }
}
