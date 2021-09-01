<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Calendar;

class Result
{
    public array $items;
    public \DateTimeImmutable $start;
    public \DateTimeImmutable $end;
    public \DateTimeImmutable $month;

    public function __construct(array $items, \DateTimeImmutable $start, \DateTimeImmutable $end, \DateTimeImmutable $month)
    {
        $this->items = $items;
        $this->start = $start;
        $this->end = $end;
        $this->month = $month;
    }
}
