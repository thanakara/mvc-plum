<?php

declare(strict_types=1);

namespace App\Services;

use App\App;
use App\Models\UsersModel;
use App\Models\AccountsModel;

class DBALAccountService
{
    public function createAccountWithUser(
        string $accountName,
        string $region,
        string $email,
        bool $isActive,
    ): void {
        $conn = App::proxy();

        try {
            $conn->transactional(
                function () use (
                    $conn,
                    $email,
                    $isActive,
                    $accountName,
                    $region,
                    &$userId,
                    &$accountId
                ) {
                    $usersModel = new UsersModel();
                    $accountsModel = new AccountsModel();

                    $userId = $usersModel->create($email, $isActive);
                    $accountId = $accountsModel->create($userId, $accountName, $region);
                }
            );
        } catch (\Throwable $e) {
            // rollback when active transaction
            if ($conn->isTransactionActive()) {
                $conn->rollback();
            }
            throw new \Exception(message: $e->getMessage());
        }
    }
}
