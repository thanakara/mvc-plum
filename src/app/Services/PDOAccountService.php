<?php

declare(strict_types=1);

namespace App\Services;

use App\App;
use App\Models\UsersModel;
use App\Models\AccountsModel;

class PDOAccountService
{
    public function createAccountWithUser(
        string $accountName,
        string $region,
        string $email,
        bool $isActive,
    ): void {
        $pdoDB = App::proxy();
        $pdoDB->beginTransaction();

        try {
            $usersModel = new UsersModel();
            $accountsModel = new AccountsModel();

            $userId = $usersModel->create(
                email: $email,
                isActive: $isActive
            );
            $accountsModel->create(
                userId: $userId,
                accountName: $accountName,
                region: $region
            );

            $pdoDB->commit();
        } catch (\Throwable $e) {
            if ($pdoDB->inTransaction()) {
                $pdoDB->rollBack();
            }
            throw new \Exception(message: $e->getMessage());
        }
    }
}
