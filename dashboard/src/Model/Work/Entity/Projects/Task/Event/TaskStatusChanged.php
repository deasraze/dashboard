<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Status;

class TaskStatusChanged
{
    public Id $taskId;
    public MemberId $actorId;
    public Status $status;

    public function __construct(Id $taskId, MemberId $actorId, Status $status)
    {
        $this->taskId = $taskId;
        $this->actorId = $actorId;
        $this->status = $status;
    }
}
