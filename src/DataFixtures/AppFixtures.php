<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setName('Produit ' . $i)
                    ->setDescription('Description du produit ' . $i)
                    ->setPrice(mt_rand(10, 100))
                    ->setStock(mt_rand(0, 50));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
