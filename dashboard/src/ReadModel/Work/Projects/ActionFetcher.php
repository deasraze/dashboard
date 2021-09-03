<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects;


use Doctrine\DBAL\Connection;

class ActionFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function allForTask(int $id): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'c.*',
                'TRIM(CONCAT(actor.name_first, \' \', actor.name_last)) AS actor_name',
                'TRIM(CONCAT(set_executor.name_first, \' \', set_executor.name_last)) AS set_executor_name',
                'TRIM(CONCAT(set_revoked_executor.name_first, \' \', set_revoked_executor.name_last)) AS set_revoked_executor_name',
                'set_project.name AS set_project_name'
            )
            ->from('work_projects_task_changes', 'c')
            ->innerJoin('c', 'work_projects_tasks', 'task', 'task.id = c.task_id')
            ->leftJoin('c', 'work_members_members', 'actor', 'actor.id = c.actor_id')
            ->leftJoin('c', 'work_members_members', 'set_executor', 'set_executor.id = c.set_executor_id')
            ->leftJoin('c', 'work_members_members', 'set_revoked_executor', 'set_revoked_executor.id = c.set_revoked_executor_id')
            ->leftJoin('c', 'work_projects_projects', 'set_project', 'set_project.id = c.set_project_id')
            ->where('c.task_id = :task')
            ->setParameter('task', $id)
            ->orderBy('date')
            ->execute()->fetchAllAssociative();
    }
}
