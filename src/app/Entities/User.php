<?php

declare(strict_types=1);

namespace App\Entities;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: "plum.users")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string")]
    private string $email;

    #[ORM\Column(name: "is_active", type: "boolean")]
    private bool $isActive;

    #[ORM\Column(name: "created_at", type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeInterface $createdAt;

    /*
    users table contain the primary key & accounts table holds the foreign key.
    OWNING side of the Bidirectional Relationship is the side that holds
    the foreign key and INVERSE the one that holds the primary key.

    That said, Entities\User is the Inverse side which has mappedBy,
    and Entities\Account is the Owning side which has inversedBy.

    EASYTOREMEMBER: owning side the one with @ManyToOne
    */
    #[ORM\OneToMany(
        mappedBy: "user",
        targetEntity: Account::class,
        cascade: ["persist", "remove"]
    )]
    private Collection $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getActivity(): bool
    {
        return $this->isActive;
    }

    public function setActivity(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return Collection<Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): self
    {
        $account->setUser(user: $this);
        $this->accounts->add($account);
        return $this;
    }
}
