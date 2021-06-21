<?php

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $this->buildSignedUpUserByEmail();
        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());
    }


    public function testAlready(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $this->buildSignedUpUserByEmail();
        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Resetting is already requested.');
        $user->requestPasswordReset($token, $now);
    }

    public function testExpired(): void
    {
        $now = new \DateTimeImmutable();
        $tokenOne = new ResetToken('token', $now->modify('+1 day'));

        $user = $this->buildSignedUpUserByEmail();
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

        $user = $this->buildUser();

        $this->expectExceptionMessage('Email is not specified.');
        $user->requestPasswordReset($token, $now);
    }

    private function buildSignedUpUserByEmail(): User
    {
        $user = $this->buildUser();
        $user->signUpByEmail(
            new Email('asd@asd.asd'),
            'hash',
            'token'
        );

        return $user;
    }

    private function buildUser(): User
    {
        return new User(Id::next(), new \DateTimeImmutable());
    }
}
