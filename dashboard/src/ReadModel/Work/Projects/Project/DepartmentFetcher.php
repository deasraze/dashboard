<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project;

use Doctrine\DBAL\Connection;

class DepartmentFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function allOfProject(string $project): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'd.id',
                'd.name',
                '(
                    SELECT COUNT(ms.member_id)
                    FROM work_projects_project_memberships AS ms
                    INNER JOIN work_projects_project_membership_departments AS md ON md.membership_id = ms.id
                    WHERE md.department_id = d.id AND ms.project_id = :project
                ) AS members_count'
            )
            ->from('work_projects_project_departments', 'd')
            ->where('d.project_id = :project')
            ->setParameter(':project', $project)
            ->orderBy('name')
            ->execute()->fetchAllAssociative();
    }
}
