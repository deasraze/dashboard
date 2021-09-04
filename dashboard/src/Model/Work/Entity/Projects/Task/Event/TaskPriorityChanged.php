<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskPriorityChanged
{
    public Id $taskId;
    public MemberId $actorId;
    public int $priority;

    public function __construct(Id $taskId, MemberId $actorId, int $priority)
    {
        $this->taskId = $taskId;
        $this->actorId = $actorId;
        $this->priority = $priority;
    }
}
