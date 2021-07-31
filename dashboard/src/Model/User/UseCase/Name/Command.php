<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Name;

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
    public string $firstName;
    /**
     * @Assert\NotBlank()
     */
    public string $lastName;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
