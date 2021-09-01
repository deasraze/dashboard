<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Calendar;

use App\ReadModel\Work\Projects\Calendar\Query\Query;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

class CalendarFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function byMonth(Query $query): Result
    {
        $month = new \DateTimeImmutable($query->year.'-'.$query->month.'-01');

        if ('0' === $month->format('w')) {
            $start = $month->modify('-6 days')->setTime(0, 0);
        } else {
            $start = $month->modify('-'.($month->format('w') - 1).' days')->setTime(0, 0);
        }

        $end = $start->modify('+34 days')->setTime(23, 59, 59);

        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select(
                't.id',
                't.name',
                'p.id AS project_id',
                'p.name AS project_name',
                'to_char(t.date, \'YYYY-MM-DD\') AS date',
                't.plan_date',
                't.start_date',
                't.end_date',
            )
            ->from('work_projects_tasks', 't')
            ->innerJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->where($qb->expr()->or(
                't.date BETWEEN :start AND :end',
                't.plan_date BETWEEN :start AND :end',
                't.start_date BETWEEN :start AND :end',
                't.end_date BETWEEN :start AND :end'
            ))
            ->setParameter(':start', $start, Types::DATETIME_MUTABLE)
            ->setParameter(':end', $end, Types::DATETIME_MUTABLE)
            ->orderBy('date');

        if (null !== $query->project) {
            $qb->andWhere('t.project_id = :project');
            $qb->setParameter(':project', $query->project);
        }

        if (null !== $query->member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 'ms.project_id = t.project_id');
            $qb->andWhere('ms.member_id = :member');
            $qb->setParameter(':member', $query->member);
        }

        $stmt = $qb->execute();

        return new Result($stmt->fetchAllAssociative(), $start, $end, $month);
    }
}
