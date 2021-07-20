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

    public function findForAuthByEmail(string $email): ?AuthView
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

    public function findForAuthByNetwork(string $network, string $identity): ?AuthView
    {
        $result = $this->connection->createQueryBuilder()
            ->select('u.id, u.email, u.password_hash, u.role, u.status')
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->execute()->fetchAssociative();

        if (!$result) {
            return null;
        }

        return $this->denormalizer->denormalize($result, AuthView::class);
    }

    public function findByEmail(string $email): ?ShortView
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id, email, role, status')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute()->fetchAssociative();

        if (!$result) {
            return null;
        }

        return $this->denormalizer->denormalize($result, ShortView::class);
    }
}
