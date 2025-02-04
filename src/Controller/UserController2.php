<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserType;

#[Route('/dashboard/user')]
class UserController2 extends AbstractController
{

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/', name: 'user_index')]
    public function index(UserRepository $userRepository): Response
    {

        return $this->render('dashboard/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/update-role/{id}', name: 'user_update_role', methods: ['POST'])]
    public function updateRole(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $newRoles = $request->request->all('roles'); // Récupérer tous les rôles sous forme de tableau

        if (!is_array($newRoles)) {
            $newRoles = [$newRoles]; // S'assurer que c'est bien un tableau
        }

        $user->setRoles($newRoles);
        $entityManager->flush();

        return $this->redirectToRoute('user_index');
    }
    #[Route('/new', name: 'user_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('dashboard/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/block/{id}', name: 'user_block', methods: ['POST'])]
    public function block(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user->isBanned()) {
            $user->removeRole('ROLE_BANNED');
        } else {
            $user->addRole('ROLE_BANNED');
        }
        $entityManager->flush();

        return $this->redirectToRoute('user_index');
    }
}
