<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ResetTokenSender
{
    private MailerInterface $mailer;
    private array $from;

    public function __construct(MailerInterface $mailer, array $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function send(Email $email, ResetToken $token): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address(...$this->from))
            ->to($email->getValue())
            ->subject('Password resetting')
            ->htmlTemplate('mail/user/reset.html.twig')
            ->context([
                'token' => $token->getToken(),
            ]);

        $this->mailer->send($email);
    }
}
