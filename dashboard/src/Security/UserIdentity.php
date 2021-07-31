<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    private string $id;
    private string $username;
    private string $password;
    private string $display;
    private string $role;
    private string $status;

    public function __construct(string $id, string $username, string $password, string $display, string $role, string $status)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->display = $display;
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

    public function getDisplay(): string
    {
        return $this->display;
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

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return
            $this->id === $user->id &&
            $this->password === $user->password &&
            $this->role === $user->role &&
            $this->status === $user->status;
    }
}
