<?php

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\ResetToken;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->viaEmail()->build();
        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());
    }


    public function testAlready(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->viaEmail()->build();
        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Resetting is already requested.');
        $user->requestPasswordReset($token, $now);
    }

    public function testExpired(): void
    {
        $now = new \DateTimeImmutable();
        $tokenOne = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->viaEmail()->build();
        $user->requestPasswordReset($tokenOne, $now);

        self::assertEquals($tokenOne, $user->getResetToken());

        $tokenTwo = new ResetToken('token', $now->modify('+3 day'));
        $user->requestPasswordReset($tokenTwo, $now->modify('+2 day'));

        self::assertEquals($tokenTwo, $user->getResetToken());
    }

    public function testWithoutEmail(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->build();

        $this->expectExceptionMessage('Email is not specified.');
        $user->requestPasswordReset($token, $now);
    }
}
