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

    public function listOfProject(string $project): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('work_projects_project_departments')
            ->where('project_id = :project')
            ->setParameter(':project', $project)
            ->orderBy('name')
            ->execute()->fetchAllKeyValue();
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

    public function allOfMember(string $member): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'p.id AS project_id',
                'p.name AS project_name',
                'd.id AS department_id',
                'd.name AS department_name',
            )
            ->from('work_projects_project_memberships', 'ms')
            ->innerJoin('ms', 'work_projects_project_membership_departments', 'msd', 'msd.membership_id = ms.id')
            ->innerJoin('msd', 'work_projects_project_departments', 'd', 'd.id = msd.department_id')
            ->innerJoin('d', 'work_projects_projects', 'p', 'p.id = d.project_id')
            ->where('ms.member_id = :member')
            ->setParameter(':member', $member)
            ->orderBy('p.sort')->addOrderBy('d.name')
            ->execute()->fetchAllAssociative();
    }
}
