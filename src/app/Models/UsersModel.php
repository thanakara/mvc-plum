<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Model;

class UsersModel extends Model
{
    /**
     * Utilize prepared statements and named parameters
     */
    public function create(string $email, bool $isActive = true): int
    {
        $stmt = $this->pdoDB->prepare(
            "INSERT INTO plum.users (email, is_active)
             VALUES (:mail, :activity)"
        );
        $stmt->bindValue("mail", $email);
        $stmt->bindValue("activity", $isActive, PDO::PARAM_BOOL);
        $stmt->execute();

        return (int) $this->pdoDB->lastInsertId();
    }
}
