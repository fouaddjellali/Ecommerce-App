<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
class MailerService
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }
    public function sendEmail(string $to, string $subject, string $content): void
    {
        $email = (new Email())
            ->from('noreply@lagrandeouverture.com')
            ->to($to)
            ->subject($subject)
            ->text($content);
        try {
            $this->mailer->send($email);
            $this->logger->info("Email envoyé à $to avec succès.");
        } catch (\Exception $e) {
            $this->logger->error("Erreur lors de l'envoi de l'email : " . $e->getMessage());
        }
    }
}
