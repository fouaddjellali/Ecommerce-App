<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order; // Assurez-vous d'avoir une entité Order ou ajustez selon votre projet

class WebhookController extends AbstractController
{
    #[Route('/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function webhook(Request $request, EntityManagerInterface $entityManager): Response
    {
        Stripe::setApiKey($this->getParameter('stripe.secret_key'));

      
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $webhookSecret = $this->getParameter('stripe.webhook_secret'); 

        try {
           
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            return new Response('Signature invalide', Response::HTTP_BAD_REQUEST);
        } catch (\UnexpectedValueException $e) {
            return new Response('Payload invalide', Response::HTTP_BAD_REQUEST);
        }

        
        $eventType = $event->type;
        $eventData = $event->data->object;

        if ($eventType === 'checkout.session.completed') {
        
            $sessionId = $eventData->id;
            $customerEmail = $eventData->customer_email ?? 'email inconnu';

            
            $order = $entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $sessionId]);

            if ($order) {
                $order->setStatus('paid'); 
                $entityManager->flush();
                return new Response('Paiement confirmé et base de données mise à jour', Response::HTTP_OK);
            }

            return new Response('Commande non trouvée', Response::HTTP_NOT_FOUND);
        }

        return new Response('Webhook reçu avec succès', Response::HTTP_OK);
    }
}
