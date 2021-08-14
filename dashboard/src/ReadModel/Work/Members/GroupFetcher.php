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

        return \array_column($stmt->fetchAllAssociative(), 'name', 'id');
    }
}
