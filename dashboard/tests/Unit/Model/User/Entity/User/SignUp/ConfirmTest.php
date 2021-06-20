<?php

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = $this->buildSignedUpUser();

        $user->confirmSignUp();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getConfirmToken());
    }

    public function testAlready(): void
    {
        $user = $this->buildSignedUpUser();

        $user->confirmSignUp();
        self::expectExceptionMessage('User is already confirmed.');
        $user->confirmSignUp();
    }

    private function buildSignedUpUser(): User
    {
        return new User(
            Id::next(),
            new \DateTimeImmutable(),
            new Email('test@test.ds'),
            'hash',
            'token'
        );
    }
}
