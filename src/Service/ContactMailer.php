<?php

namespace App\Service;

use App\Entity\Contact;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class ContactMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private string $toEmail /* injecté en service */
    ){}

    public function sendContactMessage(Contact $contact): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($contact->getEmail()))
            ->to($this->toEmail)
            ->replyTo($contact->getEmail())
            ->subject($contact->getSubject())
            ->htmlTemplate('email/contact.html.twig')
            ->context([
                'firstName' => $contact->getFirstName(),
                'lastName' => $contact->getLastName(),
                'senderEmail' => $contact->getEmail(),
                'subject' => $contact->getSubject(),
                'message' => $contact->getMessage(),
                'phone' => $contact->getPhone()
            ])
        ;

        $this->mailer->send($email);
    }
}