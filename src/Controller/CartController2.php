<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController2 extends AbstractController{

    #[Route('/my-cart', name: 'app_cart_user', methods: ['GET'])]
    public function userCart(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer le panier de l'utilisateur connecté
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('cart/user_cart.html.twig', [
            'cart' => $cart,
        ]);
    }
}