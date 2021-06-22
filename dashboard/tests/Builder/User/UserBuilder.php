<?php

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;

class UserBuilder
{
    private Id $id;
    private \DateTimeImmutable $date;

    private ?Email $email = null;
    private ?string $hash;
    private ?string $token;

    private ?string $network = null;
    private string $identity;

    public function __construct()
    {
        $this->id = Id::next();
        $this->date = new \DateTimeImmutable();
    }

    public function viaEmail(Email $email = null, string $hash = null, string $token = null): self
    {
        $clone = clone $this;
        $clone->email = $email ?? new Email('asd@asd.asd');
        $clone->hash = $hash ?? 'hash';
        $clone->token = $token ?? 'token';

        return $clone;
    }

    public function viaNetwork(string $network = null, string $identity = null): self
    {
        $clone = clone $this;
        $clone->network = $network ?? 'network';
        $clone->identity = $identity ?? '11111';

        return $clone;
    }

    public function build(): User
    {
        $user = new User(
            $this->id,
            $this->date
        );

        if (null !== $this->email) {
            $user->signUpByEmail(
                $this->email,
                $this->hash,
                $this->token
            );
        }

        if (null !== $this->network) {
            $user->signUpByNetwork(
                $this->network,
                $this->identity
            );
        }

        return $user;
    }
}
