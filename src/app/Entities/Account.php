<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "plum.accounts")]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    // Owning side (holds the FK; has inversedBy)
    #[ORM\ManyToOne(
        targetEntity: User::class,
        inversedBy: "accounts"
    )]
    #[ORM\JoinColumn(
        name: "user_id",
        referencedColumnName: "id",
        nullable: false
    )]
    private User $user;

    #[ORM\Column(name: "account_name", type: "string")]
    private string $accountName;

    #[ORM\Column(type: "string")]
    private string $region;

    // No constructor - setup setters & getters:

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): self
    {
        $this->accountName = $accountName;
        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    // Include the account relationship:
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
