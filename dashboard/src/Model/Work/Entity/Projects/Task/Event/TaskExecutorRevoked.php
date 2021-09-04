<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskExecutorRevoked
{
    public Id $taskId;
    public MemberId $actorId;
    public MemberId $executorId;

    public function __construct(Id $taskId, MemberId $actorId, MemberId $executorId)
    {
        $this->taskId = $taskId;
        $this->actorId = $actorId;
        $this->executorId = $executorId;
    }
}
