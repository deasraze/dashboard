<?php

namespace App\Model\User\Service;

class PasswordHasher
{
    public function hashing(string $value): string
    {
        $hash = \password_hash($value, PASSWORD_DEFAULT);

        if (false === $hash) {
            throw new \RuntimeException('Unable to generate hash.');
        }

        return $hash;
    }
}
