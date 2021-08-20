<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Copy;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $id;
    /**
     * @Assert\NotBlank()
     */
    public string $name;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
