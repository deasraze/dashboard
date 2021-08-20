<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Copy;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Role\Id;
use App\Model\Work\Entity\Projects\Role\RoleRepository;

class Handler
{
    private RoleRepository $roles;
    private Flusher $flusher;

    public function __construct(RoleRepository $roles, Flusher $flusher)
    {
        $this->roles = $roles;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        if ($this->roles->hasByName($command->name)) {
            throw new \DomainException('Role is already exists.');
        }

        $current = $this->roles->get(new Id($command->id));

        $role = $current->clone(Id::next(), $command->name);

        $this->roles->add($role);

        $this->flusher->flush();
    }
}
