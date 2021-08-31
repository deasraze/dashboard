<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Files\Remove;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public int $id;
    /**
     * @Assert\NotBlank()
     */
    public string $file;

    public function __construct(int $id, string $file)
    {
        $this->id = $id;
        $this->file = $file;
    }
}
