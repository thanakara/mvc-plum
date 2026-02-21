<?php

declare(strict_types=1);

namespace App\Models;

use App\Model;

class AccountsModel extends Model
{

    public function create(
        int $userId,
        string $accountName,
        string $region
    ): int {
        $stmt = $this->pdoDB->prepare(
            "INSERT INTO plum.accounts (user_id, account_name, region)
             VALUES (:userID, :acc, :reg)"
        );
        $stmt->bindValue("userID", $userId);
        $stmt->bindValue("acc", $accountName);
        $stmt->bindValue("reg", $region);
        $stmt->execute();

        return (int) $this->pdoDB->lastInsertId();
    }
}
