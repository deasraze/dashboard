<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Members\Member\Filter;

use App\Model\Work\Entity\Members\Member\Status;

class Filter
{
    public ?string $name = null;
    public ?string $email = null;
    public ?string $group = null;
    public string $status = Status::ACTIVE;
}
