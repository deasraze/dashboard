<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Detach;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $user;
    /**
     * @Assert\NotBlank()
     */
    public string $network;
    /**
     * @Assert\NotBlank()
     */
    public string $identity;

    public function __construct(string $user, string $network, string $identity)
    {
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;
    }
}
