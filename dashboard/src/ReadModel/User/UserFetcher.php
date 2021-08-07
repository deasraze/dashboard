<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use App\Model\User\Entity\User\User;
use App\ReadModel\NotFoundException;
use App\ReadModel\User\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserFetcher
{
    private Connection $connection;
    private EntityRepository $repository;
    private PaginatorInterface $paginator;
    private DenormalizerInterface $denormalizer;

    public function __construct(
        Connection $connection,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        DenormalizerInterface $denormalizer
    ) {
        $this->connection = $connection;
        $this->repository = $em->getRepository(User::class);
        $this->paginator = $paginator;
        $this->denormalizer = $denormalizer;
    }

    public function get(string $id): User
    {
        if (!$user = $this->repository->find($id)) {
            throw new NotFoundException('User is not found.');
        }

        return $user;
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'date',
                'email',
                'TRIM(CONCAT(name_last, \' \', name_first)) AS name',
                'role',
                'status',
            )
            ->from('user_users');

        if (null !== $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(name_last, \' \', name_first))', ':name'));
            $qb->setParameter(':name', '%' . \mb_strtolower($filter->name) . '%');
        }

        if (null !== $filter->email) {
            $qb->andWhere($qb->expr()->like('email', ':email'));
            $qb->setParameter(':email', '%' . \mb_strtolower($filter->email) . '%');
        }

        if (null !== $filter->role) {
            $qb->andWhere('role = :role');
            $qb->setParameter(':role', $filter->role);
        }

        if (null !== $filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        if (!\in_array($sort, ['date', 'email', 'name', 'role', 'status'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
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
            ->select(
                'id',
                'email',
                'password_hash',
                'TRIM(CONCAT(name_last, \' \', name_first)) AS name',
                'role',
                'status'
            )
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
            ->select(
                'u.id',
                'u.email',
                'u.password_hash',
                'TRIM(CONCAT(u.name_last, \' \', u.name_first)) AS name',
                'u.role',
                'u.status'
            )
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

    public function findBySignUpConfirmToken(string $token): ?ShortView
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id, email, role, status')
            ->from('user_users')
            ->where('confirm_token = :token')
            ->setParameter(':token', $token)
            ->execute()->fetchAssociative();

        if (!$result) {
            return null;
        }

        return $this->denormalizer->denormalize($result, ShortView::class);
    }
}
