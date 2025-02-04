<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);
            
            if ($user) {
                $token = $tokenGenerator->generateToken();
                
                $user->setResetToken($token);

                $entityManager->persist($user);
                $entityManager->flush();

                // Envoi de l'email avec MailHog
                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $token], true);
                
                $emailMessage = (new Email())
                    ->from('no-reply@example.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html('<p>Pour réinitialiser votre mot de passe, cliquez sur le lien suivant : <a href="' . $resetUrl . '">Réinitialiser le mot de passe</a></p>');
              
                $mailer->send($emailMessage);

                $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
            } else {
                $this->addFlash('danger', 'Aucun compte trouvé avec cet email.');
            }
        }

        return $this->render('reset_password/forgot_password.html.twig');
    }
    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Token invalide');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $user->setResetToken(null); // Supprime le token après la réinitialisation
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset_password.html.twig', ['token' => $token]);
    }

}
