<?php

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ActivateTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        self::assertTrue($user->isWait());

        $user->activate();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->activate();

        $this->expectExceptionMessage('User is already active.');
        $user->activate();
    }
}
