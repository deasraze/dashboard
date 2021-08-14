<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Members\Member;

use App\Model\Work\Entity\Members\Member\Member;
use App\ReadModel\Work\Members\Member\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class MemberFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Member::class);
        $this->paginator = $paginator;
    }

    public function find(string $id): ?Member
    {
        return $this->repository->find($id);
    }

    public function all(Filter $filter, int $page, int $limit, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'm.id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) AS name',
                'm.email',
                'g.name AS group',
                'm.status'
            )
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_members_groups', 'g', 'm.group_id = g.id');

        if (null !== $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(m.name_first, \' \', m.name_last))', ':name'));
            $qb->setParameter(':name', '%' . \mb_strtolower($filter->name) . '%');
        }

        if (null !== $filter->email) {
            $qb->andWhere($qb->expr()->like('m.email', ':email'));
            $qb->setParameter(':email', '%' . \mb_strtolower($filter->email) . '%');
        }

        if (null !== $filter->group) {
            $qb->andWhere('m.group_id = :group');
            $qb->setParameter(':group', $filter->group);
        }

        if (null !== $filter->status) {
            $qb->andWhere('m.status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        if (!\in_array($sort, ['name', 'email', 'group', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $limit);
    }
}
