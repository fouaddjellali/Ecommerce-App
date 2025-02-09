<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationService
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function sendNotification(string $userId, string $message): void
    {
        $update = new Update(
            "notifications/{$userId}", // Le topic unique pour l'utilisateur
            json_encode(['message' => $message])
        );

        $this->hub->publish($update);
    }
}
