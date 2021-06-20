<?php

namespace App\Model\User\Entity\User;

class User
{
    private string $id;
    private \DateTimeImmutable $date;
    private string $email;
    private string $password;

    public function __construct(string $id, \DateTimeImmutable $date, string $email, string $password)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->password;
    }
}
