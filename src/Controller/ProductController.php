<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'product_index')]
    public function index(ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('dashboard/product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'product_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }
        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription('test');
        $product->setPrice((float) $data['price']);
        $product->setStock((int) $data['stock']);
        $photoBase64 = $data['photo'] ?? null;
        if ($photoBase64) {
            $product->setPhoto($photoBase64);
        }

        $product->setCategory($entityManager->getRepository(Category::class)->find($data['category']));
        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Produit ajouté', 'product' => $data], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'product_edit', methods: ['POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }
        $photoBase64 = $data['photo'] ?? null;
        if ($photoBase64) {
            $product->setPhoto($photoBase64);
        }
        $product->setName($data['name']);
        //$product->setDescription($data['description']);
        $product->setPrice((float) $data['price']);
        $product->setStock((int) $data['stock']);

        $entityManager->flush();

        return new JsonResponse(['message' => 'Produit mis à jour'], Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($product);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Produit supprimé'], Response::HTTP_OK);
    }
}
