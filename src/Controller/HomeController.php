<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        // Récupérer les produits à partir de la base de données
        $categories = ['Catégorie 1', 'Catégorie 2', 'Catégorie 3']; // Exemple statique de catégories
        $featuredProducts = $productRepository->findBy([], null, 4); // Récupérer les 4 premiers produits comme "produits en vedette"

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
