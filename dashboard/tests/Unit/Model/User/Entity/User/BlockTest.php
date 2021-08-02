<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        self::assertTrue($user->isActive());

        $user->block();

        self::assertFalse($user->isActive());
        self::assertTrue($user->isBlocked());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->block();

        $this->expectExceptionMessage('User is already blocked.');
        $user->block();
    }
}
