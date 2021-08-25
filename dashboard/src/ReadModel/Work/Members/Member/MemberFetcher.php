<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Members\Member;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Status;
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
                'm.status',
                '(SELECT COUNT(ms.id) FROM work_projects_project_memberships ms WHERE ms.member_id = m.id) AS memberships_count'
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

        if (!\in_array($sort, ['name', 'email', 'group', 'memberships_count', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $limit);
    }

    public function exists(string $id): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('work_members_members')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute()->fetchOne() > 0;
    }

    public function activeGroupedList(): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'm.id',
                'CONCAT(m.name_first, \' \', m.name_last) AS name',
                'g.name AS group'
            )
            ->from('work_members_members', 'm')
            ->leftJoin('m', 'work_members_groups', 'g', 'g.id = m.group_id')
            ->where('m.status = :status')
            ->setParameter(':status', Status::ACTIVE)
            ->orderBy('g.name')->addOrderBy('name')
            ->execute()->fetchAllAssociative();
    }

    public function activeDepartmentListForProject(string $project): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'm.id',
                'CONCAT(m.name_first, \' \', m.name_last) AS name',
                'd.name AS department',
            )
            ->from('work_members_members', 'm')
            ->innerJoin('m', 'work_projects_project_memberships', 'ms', 'ms.member_id = m.id')
            ->innerJoin('ms', 'work_projects_project_membership_departments', 'msd', 'msd.membership_id = ms.id')
            ->innerJoin('msd', 'work_projects_project_departments', 'd', 'd.id = msd.department_id')
            ->where('m.status = :status AND ms.project_id = :project')
            ->setParameter(':status', Status::ACTIVE)
            ->setParameter(':projects', $project)
            ->orderBy('d.name')->addOrderBy('name')
            ->execute()->fetchAllAssociative();
    }
}
