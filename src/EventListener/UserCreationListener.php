<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\PusherService;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserCreationListener
{
    private PusherService $pusherService;

    public function __construct(PusherService $pusherService)
    {
        $this->pusherService = $pusherService;
    }

    public function postPersist(User $user, LifecycleEventArgs $args): void
    {
        $message = "Nouvel utilisateur ajouté : " . $user->getEmail();

        // Envoyer la notification via Pusher
        $this->pusherService->trigger('notifications', 'new', ['message' => $message]);
    }
}
