<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    public function __construct(
       private MailerInterface $mailer,
    ) {
    }

    public function sendMail(string $to, string $template): void
    {
        $email = (new Email())
            ->from('welearnn@outlook.com')
            ->to($to)
            ->priority(Email::PRIORITY_HIGH)
            ->subject('WeLearn Registration')
            ->html($template);

        $this->mailer->send($email);
    }

    public function buildRegistrationConfirmationTemplate(User $user): string
    {
        return /** @lang HTML */ "
            <p>Dear {$user->getFirstName()},</p>
            <p>Thank you for your registration on WeLearn platform. You can activate your account pressing the button below.</p>
            <p><a href='http://localhost:4200/activate-account/{$user->getActivationToken()}'>Confirm registration</a></p>
        ";
    }

    public function buildSetPasswordTemplate(User $user): string
    {
        return /** @lang HTML */ "
            <p>Dear {$user->getFirstName()},</p>
            <p>Thank you for your registration on WeLearn platform. You can activate your account and set your password pressing the button below.</p>
            <p><a href='http://localhost:4200/set-password/{$user->getActivationToken()}'>Confirm registration</a></p>
        ";
    }
}