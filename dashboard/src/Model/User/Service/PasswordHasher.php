<?php

declare(strict_types=1);

namespace App\Model\User\Service;

class PasswordHasher
{
    public function hashing(string $password): string
    {
        $hash = \password_hash($password, PASSWORD_DEFAULT);

        if (false === $hash) {
            throw new \RuntimeException('Unable to generate hash.');
        }

        return $hash;
    }

    public function verify(string $password, string $hash): bool
    {
        return \password_verify($password, $hash);
    }
}
