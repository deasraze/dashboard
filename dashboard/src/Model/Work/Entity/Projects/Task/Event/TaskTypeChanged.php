<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Type;

class TaskTypeChanged
{
    public Id $taskId;
    public MemberId $actorId;
    public Type $type;

    public function __construct(Id $taskId, MemberId $actorId, Type $type)
    {
        $this->taskId = $taskId;
        $this->actorId = $actorId;
        $this->type = $type;
    }
}
