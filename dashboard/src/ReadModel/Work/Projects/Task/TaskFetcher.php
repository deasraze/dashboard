<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Task;

use App\ReadModel\Work\Projects\Task\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class TaskFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $limit, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                't.id',
                't.date',
                't.author_id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) AS author_name',
                't.project_id',
                'p.name AS project_name',
                't.name',
                't.parent_id AS parent',
                't.type',
                't.priority',
                't.progress',
                't.plan_date',
                't.status',
            )
            ->from('work_projects_tasks', 't')
            ->innerJoin('t', 'work_members_members', 'm', 'm.id = t.author_id')
            ->innerJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id');

        if (null !== $filter->member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 'ms.project_id = t.project_id');
            $qb->andWhere('ms.member_id = :member');
            $qb->setParameter(':member', $filter->member);
        }

        if (null !== $filter->project) {
            $qb->andWhere('t.project_id = :project');
            $qb->setParameter(':project', $filter->project);
        }

        if (null !== $filter->author) {
            $qb->andWhere('t.author_id = :author');
            $qb->setParameter(':author', $filter->author);
        }

        if (null !== $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(t.name)', ':name'));
            $qb->setParameter(':name', '%' . \mb_strtolower($filter->name) . '%');
        }

        if (null !== $filter->type) {
            $qb->andWhere('t.type = :type');
            $qb->setParameter(':type', $filter->type);
        }

        if (null !== $filter->priority) {
            $qb->andWhere('t.priority = :priority');
            $qb->setParameter(':priority', $filter->priority);
        }

        if (null !== $filter->status) {
            $qb->andWhere('t.status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        if (null !== $filter->executor) {
            $qb->innerJoin('t', 'work_projects_tasks_executors', 'e', 'e.task_id = t.id');
            $qb->andWhere('e.member_id = :executor');
            $qb->setParameter(':executor', $filter->executor);
        }

        if (!\in_array($sort, ['t.id', 't.date', 'author_name', 'project_name', 'name', 't.type', 't.plan_date', 't.progress', 't.priority', 't.status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        $pagination = $this->paginator->paginate($qb, $page, $limit);

        $tasks = (array) $pagination->getItems();
        $executors = $this->batchLoadExecutors(\array_column($tasks, 'id'));

        $pagination->setItems($this->mergeTasksWithExecutors($tasks, $executors));

        return $pagination;
    }

    public function childrenOf(int $task): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                't.id',
                't.date',
                't.project_id',
                'p.name AS project_name',
                't.name',
                't.parent_id AS parent',
                't.type',
                't.priority',
                't.progress',
                't.plan_date',
                't.status',
            )
            ->from('work_projects_tasks', 't')
            ->innerJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->where('t.parent_id = :parent')
            ->setParameter(':parent', $task)
            ->orderBy('date', 'desc')
            ->execute();

        $tasks = $stmt->fetchAllAssociative();
        $executors = $this->batchLoadExecutors(\array_column($tasks, 'id'));

        return $this->mergeTasksWithExecutors($tasks, $executors);
    }

    private function batchLoadExecutors(array $ids): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'e.task_id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) AS name'
            )
            ->from('work_projects_tasks_executors', 'e')
            ->innerJoin('e', 'work_members_members', 'm', 'm.id = e.member_id')
            ->where('e.task_id IN (:tasks)')
            ->setParameter(':tasks', $ids, Connection::PARAM_INT_ARRAY)
            ->orderBy('name')
            ->execute()->fetchAllAssociative();
    }

    private function mergeTasksWithExecutors(array $tasks, array $executors): array
    {
        return \array_map(static function (array $task) use ($executors) {
            return \array_merge($task, [
                'executors' => \array_filter($executors, static function (array $executor) use ($task): bool {
                    return $executor['task_id'] === $task['id'];
                })
            ]);
        }, $tasks);
    }
}
