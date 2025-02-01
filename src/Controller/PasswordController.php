<?php

// src/Controller/PasswordResetController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User; 

class PasswordResetController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            // Recherche de l'utilisateur dans la base de données
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un token de réinitialisation sécurisé
                $resetToken = bin2hex(random_bytes(32));

                // Enregistrer ce token dans l'entité User
                $user->setResetToken($resetToken);
                $entityManager->flush(); // Sauvegarder les modifications

                // Générer le lien de réinitialisation
                $resetUrl = $this->generateUrl('reset_password', ['token' => $resetToken], true);

                // Envoyer l'email
                $emailMessage = (new Email())
                    ->from('noreply@yourdomain.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de mot de passe')
                    ->text("Cliquez sur ce lien pour réinitialiser votre mot de passe : $resetUrl");

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Un email vous a été envoyé.');
                return $this->redirectToRoute('login');
            }

            $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email.');
        }

        return $this->render('security/forgot_password.html.twig');
    }
}
