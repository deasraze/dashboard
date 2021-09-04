<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Event;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Task\Id;

class TaskEdited
{
    public Id $taskId;
    public MemberId $actorId;
    public string $name;
    public ?string $content;

    public function __construct(Id $taskId, MemberId $actorId, string $name, ?string $content)
    {
        $this->taskId = $taskId;
        $this->actorId = $actorId;
        $this->name = $name;
        $this->content = $content;
    }
}
