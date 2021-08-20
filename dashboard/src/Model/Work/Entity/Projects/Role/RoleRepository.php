<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Role;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class RoleRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Role::class);
    }

    public function get(Id $id): Role
    {
        if (!$role = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Role is not found.');
        }

        return $role;
    }

    public function hasByName(string $name): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.name)')
            ->where('t.name = :name')
            ->setParameter(':name', $name)
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(Role $role): void
    {
        $this->em->persist($role);
    }

    public function remove(Role $role): void
    {
        $this->em->remove($role);
    }
}
