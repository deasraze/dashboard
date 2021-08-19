<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project;

use App\ReadModel\Work\Projects\Project\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProjectFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function getMaxSort(): int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('MAX(p.sort) AS m')
            ->from('work_projects_projects', 'p')
            ->execute()->fetchOne();
    }

    public function all(Filter $filter, int $page, int $limit, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.name',
                'p.status'
            )
            ->from('work_projects_projects', 'p');

        if (null !== $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(p.name)', ':name'));
            $qb->setParameter(':name', '%' . \mb_strtolower($filter->name) . '%');
        }

        if (null !== $filter->status) {
            $qb->andWhere('p.status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        if (!\in_array($sort, ['name', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $limit);
    }
}
