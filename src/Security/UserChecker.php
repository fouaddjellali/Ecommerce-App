<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (method_exists($user, 'isBanned') && $user->isBanned()) {
            throw new CustomUserMessageAuthenticationException('Votre compte est banni.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Pas besoin d’ajouter une logique après l’authentification
    }
}
