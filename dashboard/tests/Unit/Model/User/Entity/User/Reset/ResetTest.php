<?php

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $this->buildSignedUpUserByEmail();
        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());

        $user->passwordReset($now, $hash = 'hash');

        self::assertNull($user->getResetToken());
        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testExpiredToken(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now);

        $user = $this->buildSignedUpUserByEmail();
        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Reset token is expired.');
        $user->passwordReset($now->modify('+1 day'), 'hash');
    }

    public function testNotRequested(): void
    {
        $now = new \DateTimeImmutable();
        $user = $this->buildSignedUpUserByEmail();

        $this->expectExceptionMessage('Resetting is not requested.');
        $user->passwordReset($now, 'hash');
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
