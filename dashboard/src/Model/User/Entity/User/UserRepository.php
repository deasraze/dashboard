<?php

namespace App\Model\User\Entity\User;

interface UserRepository
{
    public function findByConfirmToken(string $token): ?User;

    public function hasByEmail(Email $email): bool;

    public function add(User $user): void;
}
