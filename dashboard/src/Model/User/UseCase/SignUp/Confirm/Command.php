<?php

namespace App\Model\User\UseCase\SignUp\Confirm;

class Command
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}
