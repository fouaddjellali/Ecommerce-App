<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CartController2 extends AbstractController
{
    #[Route('/my-cart', name: 'app_cart_user', methods: ['GET'])]
    public function userCart(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('cart/user_cart.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/my-cart/remove/{itemId}', name: 'app_cart_user_item_delete', methods: ['POST'])]
    public function removeItem(EntityManagerInterface $entityManager, $itemId): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            return $this->redirectToRoute('app_home');
        }

        $cartItem = $entityManager->getRepository(CartItem::class)->find($itemId);

        if ($cartItem) {
            // Si la quantité est supérieure à 1, on la réduit de 1
            if ($cartItem->getQuantity() > 1) {
                $cartItem->setQuantity($cartItem->getQuantity() - 1);
            } else {
                // Si la quantité est égale à 1, on supprime l'élément du panier
                $cart->removeItem($cartItem);
                $entityManager->remove($cartItem);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Produit mis à jour dans votre panier.');
        } else {
            $this->addFlash('error', 'Produit introuvable.');
        }

        return $this->redirectToRoute('app_cart_user');
    }

    #[Route('/my-cart/add/{itemId}', name: 'app_cart_user_item_add', methods: ['POST'])]
    public function addItem(EntityManagerInterface $entityManager, $itemId): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            return $this->redirectToRoute('app_home');
        }

        $cartItem = $entityManager->getRepository(CartItem::class)->find($itemId);

        if ($cartItem) {
            // On augmente la quantité de 1
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
            $entityManager->flush();
            $this->addFlash('success', 'Quantité du produit mise à jour.');
        } else {
            $this->addFlash('error', 'Produit introuvable.');
        }

        return $this->redirectToRoute('app_cart_user');
    }
}
