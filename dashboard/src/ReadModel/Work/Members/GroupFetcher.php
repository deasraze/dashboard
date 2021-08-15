<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Members;

use Doctrine\DBAL\Connection;

class GroupFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function all(): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'g.id',
                'g.name',
                '(SELECT COUNT(m.id) FROM work_members_members AS m WHERE m.group_id = g.id) AS members'
            )
            ->from('work_members_groups', 'g')
            ->orderBy('name')
            ->execute()->fetchAllAssociative();
    }

    public function assoc(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id, name')
            ->from('work_members_groups')
            ->orderBy('name')
            ->execute();

        return $stmt->fetchAllKeyValue();
    }
}
