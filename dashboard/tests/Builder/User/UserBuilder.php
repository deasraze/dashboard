<?php

declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;

class UserBuilder
{
    private Id $id;
    private \DateTimeImmutable $date;
    private Name $name;

    private ?Email $email = null;
    private ?string $hash = null;
    private ?string $token = null;
    private bool $confirmed = false;

    private ?string $network = null;
    private ?string $identity = null;

    private ?Role $role = null;

    public function __construct()
    {
        $this->id = Id::next();
        $this->date = new \DateTimeImmutable();
        $this->name = new Name('First', 'Last');
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

    public function confirmed(): self
    {
        $clone = clone $this;
        $clone->confirmed = true;

        return $clone;
    }

    public function withId(Id $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    public function withName(Name $name): self
    {
        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    public function withRole(Role $role): self
    {
        $clone = clone $this;
        $clone->role = $role;

        return $clone;
    }

    public function build(): User
    {
        $user = null;

        if (null !== $this->email) {
            $user = User::signUpByEmail(
                $this->id,
                $this->date,
                $this->name,
                $this->email,
                $this->hash,
                $this->token
            );

            if ($this->confirmed) {
                $user->confirmSignUp();
            }
        }

        if (null !== $this->network) {
            $user = User::signUpByNetwork(
                $this->id,
                $this->date,
                $this->name,
                $this->network,
                $this->identity
            );
        }

        if (null === $user) {
            throw new \BadMethodCallException('Specify via method.');
        }

        if (null !== $this->role) {
            $user->changeRole($this->role);
        }

        return $user;
    }
}
