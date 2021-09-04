<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskProgressChanged
{
    public Id $taskId;
    public MemberId $actorId;
    public int $progress;

    public function __construct(Id $taskId, MemberId $actorId, int $progress)
    {
        $this->taskId = $taskId;
        $this->actorId = $actorId;
        $this->progress = $progress;
    }
}
