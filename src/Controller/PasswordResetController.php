<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerService;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordResetController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(Request $request, MailerService $mailer, EntityManagerInterface $entityManager): Response
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
                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $resetToken], true);

                // Envoyer l'email
                $mailer->sendEmail(
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    "Cliquez sur ce lien pour réinitialiser votre mot de passe : " . $resetUrl
                );

                $this->addFlash('success', 'Un email vous a été envoyé.');
                return $this->redirectToRoute('login');
            }
            $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email.');
        }
        return $this->render('security/forgot_password.html.twig');
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
