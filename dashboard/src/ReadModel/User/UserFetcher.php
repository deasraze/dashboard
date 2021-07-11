<?php

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserFetcher
{
    private Connection $connection;
    private DenormalizerInterface $denormalizer;

    public function __construct(Connection $connection, DenormalizerInterface $denormalizer)
    {
        $this->connection = $connection;
        $this->denormalizer = $denormalizer;
    }

    public function existsByResetToken(string $token): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('user_users')
            ->where('reset_token_token = :token')
            ->setParameter(':token', $token)
            ->execute()->fetchOne() > 0;
    }

    public function findForAuth(string $email): ?AuthView
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id, email, password_hash, role, status')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute()->fetchAssociative();

        if (!$result) {
            return null;
        }

        return $this->denormalizer->denormalize($result, AuthView::class);
    }
}
