<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Task;
use App\ReadModel\Comment\CommentRow;
use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CommentFetcher
{
    private Connection $connection;
    private DenormalizerInterface $denormalizer;

    public function __construct(Connection $connection, DenormalizerInterface $denormalizer)
    {
        $this->connection = $connection;
        $this->denormalizer = $denormalizer;
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

        return $this->denormalizer->denormalize($result, CommentRow::class.'[]');
    }
}
