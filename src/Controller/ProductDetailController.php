<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductDetailController extends AbstractController
{
    #[Route('/product/{id}', name: 'product_detail')]
    public function detail(Product $product): Response
    {
        return $this->render('product_detail/index.html.twig', [
            'product' => $product,
        ]);
    }
}
