<?php

namespace App\Security;

use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, PasswordAuthenticatedUserInterface
{
    private string $id;
    private string $username;
    private string $password;
    private string $role;
    private string $status;

    public function __construct(string $id, string $username, string $password, string $role, string $status)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getUsername(): ?string
    {
        return null;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function isActive(): bool
    {
        return ($this->status === User::STATUS_ACTIVE);
    }
}
