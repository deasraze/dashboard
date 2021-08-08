<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Group;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class GroupRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Group::class);
    }

    public function get(Id $id): Group
    {
        if (!$group = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Group is not found.');
        }

        return $group;
    }

    public function add(Group $group): void
    {
        $this->em->persist($group);
    }

    public function remove(Group $group): void
    {
        $this->em->remove($group);
    }
}
