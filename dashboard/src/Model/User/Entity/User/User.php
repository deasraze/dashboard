<?php

namespace App\Model\User\Entity\User;

class User
{
    private Id $id;
    private \DateTimeImmutable $date;
    private Email $email;
    private string $hash;

    public function __construct(Id $id, \DateTimeImmutable $date, Email $email, string $hash)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->hash = $hash;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->hash;
    }
}
