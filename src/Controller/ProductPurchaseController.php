<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductPurchaseController extends AbstractController
{
    #[Route('/product/{id}/purchase', name: 'product_purchase')]
    public function purchase(Product $product, EntityManagerInterface $em): Response
    {
        if ($product->getStock() > 0) {
            $product->setStock($product->getStock() - 1);
            $em->flush();

            $this->addFlash('success', 'Produit acheté avec succès !');
        } else {
            $this->addFlash('error', 'Produit en rupture de stock.');
        }

        return $this->redirectToRoute('product_list');
    }
}
