<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\User;
use App\Entities\Account;
use Doctrine\ORM\EntityManagerInterface;

class ORMAccountService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function createAccountWithUser(
        string $email,
        bool $isActive,
        string $accountName,
        string $region,
    ): void {
        $this->em->wrapInTransaction(
            function () use (
                $email,
                $isActive,
                $accountName,
                $region
            ) {
                $user = new User();
                $user
                    ->setEmail($email)
                    ->setActivity($isActive);

                $account = new Account();
                $account
                    ->setAccountName($accountName)
                    ->setRegion($region);

                /*
                addAccount internally calls $account->setUser($this);
                so the owning side is set correctly
                */
                $user->addAccount($account);

                /*
                persisting user is enough because of cascade: ["persist"];
                Doctrine persists the account automatically
                */
                $this->em->persist($user);
                $this->em->flush();
            }
        );
    }
}
