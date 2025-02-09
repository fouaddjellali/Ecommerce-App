<?php
// src/Controller/StripeController.php
namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\Entity\OrderItem;
class StripeController extends AbstractController
{
    #[Route('/create-checkout-session', name: 'stripe_checkout', methods: ['POST','GET'])]
    public function checkout(EntityManagerInterface $entityManager): JsonResponse
    {
        Stripe::setApiKey($this->getParameter('stripe.secret_key'));

        $user = $this->getUser();
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart || count($cart->getItems()) === 0) {
            return new JsonResponse(['error' => 'Votre panier est vide'], Response::HTTP_BAD_REQUEST);
        }

        $lineItems = [];
        foreach ($cart->getItems() as $cartItem) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $cartItem->getProduct()->getName(),
                    ],
                    'unit_amount' => $cartItem->getProduct()->getPrice() * 100, 
                ],
                'quantity' => $cartItem->getQuantity(),
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('stripe_success', [], 0),
            'cancel_url' => $this->generateUrl('stripe_cancel', [], 0),
        ]);
        return new JsonResponse(['id' => $session->id]);
    }


    #[Route('/success', name: 'stripe_success')]
    public function success(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart || count($cart->getItems()) === 0) {
            return $this->redirectToRoute('app_cart_user');
        }


        $order = new Order();
        $order->setUser($user);
        $order->setStatus('paid');

        foreach ($cart->getItems() as $cartItem) {
            $product = $cartItem->getProduct();

         
            if ($product->getStock() < $cartItem->getQuantity()) {
                $this->addFlash('error', "Stock insuffisant pour le produit: " . $product->getName());
                return $this->redirectToRoute('app_cart_user');
            }

            $product->setStock($product->getStock() - $cartItem->getQuantity());
            $entityManager->persist($product);

            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($product);
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($product->getPrice());
            $order->addItem($orderItem);
        }

        $entityManager->persist($order);
        $entityManager->flush();

     
        foreach ($cart->getItems() as $cartItem) {
            $entityManager->remove($cartItem);
        }
        $entityManager->flush();

        return $this->render('stripe/success.html.twig', [
            'order' => $order
        ]);
    }
    #[Route('/cancel', name: 'stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }
}
