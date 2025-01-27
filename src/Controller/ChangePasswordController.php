<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ChangePasswordType;
use App\Entity\User; // Assurez-vous d'importer votre entité User
use Doctrine\ORM\EntityManagerInterface; // Import du gestionnaire d'entités
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ChangePasswordController extends AbstractController
{
    #[Route('/change-password', name: 'change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager // Injection du gestionnaire d'entités
    ): Response {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        // Vérifiez que l'utilisateur est une instance de votre entité User
        if (!$user instanceof User) {
            throw new AccessDeniedException('Vous devez être connecté pour changer votre mot de passe.');
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            // Utiliser le gestionnaire d'entités pour sauvegarder les modifications
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe changé avec succès !');
            return $this->redirectToRoute('profile');
        }

        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
