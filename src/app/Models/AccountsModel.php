<?php

declare(strict_types=1);

namespace App\Models;

use App\Model;
use Doctrine\DBAL\ParameterType;

class AccountsModel extends Model
{

    public function create(
        int $userId,
        string $accountName,
        string $region
    ): int {
        // $stmt = $this->pdoDB->prepare(
        //     "INSERT INTO plum.accounts (user_id, account_name, region)
        //      VALUES (:userID, :acc, :reg)"
        // );
        // $stmt->bindValue("userID", $userId);
        // $stmt->bindValue("acc", $accountName);
        // $stmt->bindValue("reg", $region);
        // $stmt->execute();
        $builder = $this->dbalDB->createQueryBuilder();
        $stmt = $builder
            ->insert("plum.accounts")
            ->values(
                [
                    "user_id" => ":userId",
                    "account_name" => ":accountName",
                    "region" => ":region",
                ]
            )
            ->setParameter("userId", $userId, ParameterType::INTEGER)
            ->setParameter("accountName", $accountName)
            ->setParameter("region", $region);
        $stmt->executeStatement();

        // return (int) $this->pdoDB->lastInsertId();
        return (int) $this->dbalDB->lastInsertId();
    }
}
