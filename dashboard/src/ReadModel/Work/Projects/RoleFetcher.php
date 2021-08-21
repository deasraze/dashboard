<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects;

use Doctrine\DBAL\Connection;

class RoleFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function all(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'r.id',
                'r.name',
                'r.permissions',
                '(SELECT COUNT(*) FROM work_projects_project_membership_roles AS mr WHERE mr.role_id = r.id) AS members_count'
            )
            ->from('work_projects_roles', 'r')
            ->orderBy('name')
            ->execute();

        return \array_map(static function (array $role): array {
            return \array_replace($role, [
                'permissions' => \json_decode($role['permissions'], true)
            ]);
        }, $stmt->fetchAllAssociative());
    }

    public function allList(): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('work_projects_roles')
            ->orderBy('name')
            ->execute()->fetchAllKeyValue();
    }
}
