<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Task;
use App\ReadModel\Comment\CommentRow;
use Doctrine\DBAL\Connection;

class CommentFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function allForTask(int $id): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'c.id',
                'c.date',
                'm.id AS author_id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) AS author_name',
                'm.email AS author_email',
                'c.text',
            )
            ->from('comment_comments', 'c')
            ->innerJoin('c', 'work_members_members', 'm', 'm.id = c.author_id')
            ->where('c.entity_type = :entity_type AND c.entity_id = :entity_id')
            ->setParameter('entity_type', Task::class)
            ->setParameter('entity_id', $id)
            ->orderBy('date')
            ->execute()->fetchAllAssociative();

        return \array_map(static function (array $item): CommentRow {
            $commentRow = new CommentRow();

            $commentRow->id = $item['id'];
            $commentRow->date = $item['date'];
            $commentRow->author_id = $item['author_id'];
            $commentRow->author_name = $item['author_name'];
            $commentRow->author_email = $item['author_email'];
            $commentRow->text = $item['text'];

            return $commentRow;
        }, $result);
    }
}
