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

#[Route('/dashboard/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'order_index')]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('dashboard/order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/update/{id}', name: 'order_update', methods: ['POST'])]
    public function update(Request $request, Order $order, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['status'])) {
            return new JsonResponse(['message' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        $order->setStatus($data['status']);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Statut mis à jour'], Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'order_delete', methods: ['DELETE'])]
    public function delete(Order $order, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($order);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Commande supprimée'], Response::HTTP_OK);
    }
}
