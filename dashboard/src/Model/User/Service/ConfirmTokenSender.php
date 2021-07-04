<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ConfirmTokenSender
{
    private MailerInterface $mailer;
    private array $from;

    public function __construct(MailerInterface $mailer, array $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function send(Email $email, string $token): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address(...$this->from))
            ->to($email->getValue())
            ->subject('Sign Up Confirmation')
            ->htmlTemplate('mail/user/signup.html.twig')
            ->context([
                'token' => $token
            ]);

        $this->mailer->send($email);
    }
}
