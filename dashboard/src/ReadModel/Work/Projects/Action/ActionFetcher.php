<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Action;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ActionFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $limit): PaginationInterface
    {
        $qb = $this->createQb();

        if (null !== $filter->project) {
            $qb->andWhere('project.id = :project OR set_project.id = :project');
            $qb->setParameter('project', $filter->project);
        }

        if (null !== $filter->member) {
            $qb->innerJoin('project', 'work_projects_project_memberships', 'membership', 'membership.project_id = project.id');
            $qb->andWhere('membership.member_id = :member');
            $qb->setParameter('member', $filter->member);
        }

        $qb->orderBy('date', 'desc');

        return $this->paginator->paginate($qb, $page, $limit);
    }

    public function allForTask(int $id): array
    {
        return $this->createQb()
            ->where('task.id = :task')
            ->setParameter('task', $id)
            ->orderBy('date')
            ->execute()->fetchAllAssociative();
    }

    private function createQb(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'c.*',
                'task.name AS task_name',
                'project.id AS project_id',
                'project.name AS project_name',
                'set_project.name AS set_project_name',
                'TRIM(CONCAT(actor.name_first, \' \', actor.name_last)) AS actor_name',
                'TRIM(CONCAT(set_executor.name_first, \' \', set_executor.name_last)) AS set_executor_name',
                'TRIM(CONCAT(set_revoked_executor.name_first, \' \', set_revoked_executor.name_last)) AS set_revoked_executor_name',
            )
            ->from('work_projects_task_changes', 'c')
            ->innerJoin('c', 'work_projects_tasks', 'task', 'task.id = c.task_id')
            ->innerJoin('task', 'work_projects_projects', 'project', 'project.id = task.project_id')
            ->leftJoin('c', 'work_members_members', 'actor', 'actor.id = c.actor_id')
            ->leftJoin('c', 'work_members_members', 'set_executor', 'set_executor.id = c.set_executor_id')
            ->leftJoin('c', 'work_members_members', 'set_revoked_executor', 'set_revoked_executor.id = c.set_revoked_executor_id')
            ->leftJoin('c', 'work_projects_projects', 'set_project', 'set_project.id = c.set_project_id');
    }
}
