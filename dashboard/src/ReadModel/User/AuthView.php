<?php

declare(strict_types=1);

namespace App\ReadModel\User;

class AuthView
{
    public string $id;
    public ?string $email = null;
    public ?string $password_hash = null;
    public string $name;
    public string $role;
    public string $status;
}
