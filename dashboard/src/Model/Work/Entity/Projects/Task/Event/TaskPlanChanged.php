<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskPlanChanged
{
    public Id $taskId;
    public MemberId $actorId;
    public ?\DateTimeImmutable $planDate;

    public function __construct(Id $taskId, MemberId $actorId, ?\DateTimeImmutable $planDate)
    {
        $this->taskId = $taskId;
        $this->actorId = $actorId;
        $this->planDate = $planDate;
    }
}
