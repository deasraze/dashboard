<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class NewEmailConfirmTokenSender
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Email $email, string $token): void
    {
        $email = (new TemplatedEmail())
            ->to($email->getValue())
            ->subject('Email changing confirmation')
            ->htmlTemplate('mail/user/email.html.twig')
            ->context([
                'token' => $token,
            ]);

        $this->mailer->send($email);
    }
}
