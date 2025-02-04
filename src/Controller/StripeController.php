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

class StripeController extends AbstractController
{
    #[Route('/create-checkout-session', name: 'stripe_checkout')]
    public function checkout(Request $request): JsonResponse
    {
        Stripe::setApiKey($this->getParameter('stripe.secret_key'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Produit Test',
                    ],
                    'unit_amount' => 1000, // Prix en centimes (10€)
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('stripe_success', [], 0),
            'cancel_url' => $this->generateUrl('stripe_cancel', [], 0),
        ]);

        return new JsonResponse(['id' => $session->id]);
    }

    #[Route('/success', name: 'stripe_success')]
    public function success(): Response
    {
        return $this->render('stripe/success.html.twig');
    }

    #[Route('/cancel', name: 'stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }
}
