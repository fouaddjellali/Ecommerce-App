<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Fetch categories from the database
        $categories = $entityManager
            ->getRepository(Category::class)
            ->findAll();

        // Fetch featured products from the database (you may want to adjust this query if you have specific criteria for featured products)
        $featuredProducts = $entityManager
            ->getRepository(Product::class)
            ->findBy([], ['id' => 'DESC'], 5);  // Get the last 5 products (or modify this to suit your needs)

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
