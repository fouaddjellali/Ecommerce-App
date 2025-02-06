<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders')]
class OrderController2 extends AbstractController
{
    #[Route('', name: 'app_orders')]
    public function orders(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $orders = $entityManager->getRepository(Order::class)->findBy(['user' => $user]);

        return $this->render('user/orders.html.twig', [
            'orders' => $orders
        ]);
    }
}
