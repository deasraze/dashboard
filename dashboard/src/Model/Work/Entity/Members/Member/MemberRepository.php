<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Member;

use App\Model\EntityNotFoundException;
use App\Model\Work\Entity\Members\Group\Id as GroupId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class MemberRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(Member::class);
    }

    public function get(Id $id): Member
    {
        if (!$member = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Member is not found.');
        }

        return $member;
    }

    public function has(Id $id): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->where('t.id = :id')
                ->setParameter(':id', $id->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByGroup(GroupId $id): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->where('t.group = :group')
                ->setParameter(':group', $id->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(Member $member): void
    {
        $this->em->persist($member);
    }
}
