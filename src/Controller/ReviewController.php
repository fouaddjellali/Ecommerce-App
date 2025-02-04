<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/review')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'review_index')]
    public function index(ReviewRepository $reviewRepository): Response
    {
        return $this->render('dashboard/review/index.html.twig', [
            'reviews' => $reviewRepository->findAll(),
        ]);
    }

    #[Route('/delete/{id}', name: 'review_delete', methods: ['DELETE'])]
    public function delete(Review $review, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($review);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Avis supprimé'], Response::HTTP_OK);
    }
}
