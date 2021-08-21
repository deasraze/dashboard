<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\EntityNotFoundException;
use App\Model\Work\Entity\Projects\Role\Id as RoleId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ProjectRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Project::class);
    }

    public function get(Id $id): Project
    {
        if (!$project = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Project is not found.');
        }

        return $project;
    }

    public function hasMembersWithRole(RoleId $id): bool
    {
        return $this->repo->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->innerJoin('p.memberships', 'ms')
            ->innerJoin('ms.roles', 'r')
            ->where('r.id = :role')
            ->setParameter(':role', $id->getValue())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(Project $project): void
    {
        $this->em->persist($project);
    }

    public function remove(Project $project): void
    {
        $this->em->remove($project);
    }
}
