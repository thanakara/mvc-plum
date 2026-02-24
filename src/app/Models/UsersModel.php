<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Model;
use Doctrine\DBAL\ParameterType;

class UsersModel extends Model
{
    /**
     * Utilize prepared statements and named parameters
     */
    public function create(string $email, bool $isActive = true): int
    {
        // $stmt = $this->pdoDB->prepare(
        //     "INSERT INTO plum.users (email, is_active)
        //      VALUES (:mail, :activity)"
        // );
        // $stmt->bindValue("mail", $email);
        // $stmt->bindValue("activity", $isActive, PDO::PARAM_BOOL);
        // $stmt->execute();
        $builder = $this->dbalDB->createQueryBuilder();
        $stmt = $builder
            ->insert("plum.users")
            ->values(
                [
                    "email" => ":email",
                    "is_active" => ":isActive",
                ]
            )
            ->setParameter("email", $email)
            ->setParameter("isActive", $isActive, ParameterType::BOOLEAN);
        $stmt->executeStatement();

        // return (int) $this->pdoDB->lastInsertId();
        return (int) $this->dbalDB->lastInsertId();
    }
}
