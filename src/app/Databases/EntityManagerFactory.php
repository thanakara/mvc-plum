<?php

declare(strict_types=1);

namespace App\Databases;

use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;

class EntityManagerFactory
{
    public static function create(array $config): EntityManagerInterface
    {
        return new EntityManager(
            conn: DriverManager::getConnection($config),
            config: ORMSetup::createAttributeMetadataConfiguration(
                paths: [dirname(__DIR__) . "/../Entities"]
            )
        );
    }
}
