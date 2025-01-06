<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Données dynamiques pour les catégories et les produits
        $categories = ['Catégorie 1', 'Catégorie 2', 'Catégorie 3'];
        $featuredProducts = [
            ['name' => 'Produit 1', 'description' => 'Description du produit 1'],
            ['name' => 'Produit 2', 'description' => 'Description du produit 2'],
        ];

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
