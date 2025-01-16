<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Review;
use App\Entity\Address;
use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Stocker les références pour les relations
        $users = [];
        $categories = [];
        $products = [];
        $carts = [];

        // Créer des utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email)
                ->setPassword(password_hash('password', PASSWORD_BCRYPT))
                ->setRoles(['ROLE_USER'])
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName);

            $manager->persist($user);
            $users[] = $user;
        }

        // Créer des catégories
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setName($faker->word)
                ->setDescription($faker->sentence);

            $manager->persist($category);
            $categories[] = $category;
        }

        // Créer des produits
        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName($faker->word) // Remplacement de `productName` par `word`
                ->setDescription($faker->paragraph)
                ->setPrice($faker->randomFloat(2, 10, 200))
                ->setStock($faker->numberBetween(1, 100))
                ->setImage($faker->imageUrl(640, 480, 'technics'))
                ->setCategory($faker->randomElement($categories));

            $manager->persist($product);
            $products[] = $product;
        }

        // Créer des adresses pour les utilisateurs
        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                $address = new Address();
                $address->setUser($user)
                    ->setStreet($faker->streetAddress)
                    ->setCity($faker->city)
                    ->setState($faker->state)
                    ->setZipCode($faker->postcode)
                    ->setCountry($faker->country)
                    ->setDefault($i === 0);

                $manager->persist($address);
            }
        }

        // Créer des avis pour les produits
        foreach ($products as $product) {
            for ($i = 0; $i < 5; $i++) {
                $review = new Review();
                $review->setProduct($product)
                    ->setUser($faker->randomElement($users))
                    ->setRating($faker->numberBetween(1, 5))
                    ->setComment($faker->sentence);

                $manager->persist($review);
            }
        }

        // Créer des paniers pour les utilisateurs
        foreach ($users as $user) {
            $cart = new Cart();
            $cart->setUser($user);

            $manager->persist($cart);
            $carts[] = $cart;

            for ($i = 0; $i < 3; $i++) {
                $cartItem = new CartItem();
                $cartItem->setCart($cart)
                    ->setProduct($faker->randomElement($products))
                    ->setQuantity($faker->numberBetween(1, 5));

                $manager->persist($cartItem);
            }
        }

        // Créer des commandes
        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $order = new Order();
                $order->setUser($user)
                    ->setStatus($faker->randomElement(['Pending', 'Processing', 'Completed']));

                $manager->persist($order);

                for ($j = 0; $j < 3; $j++) {
                    $orderItem = new OrderItem();
                    $orderItem->setOrder($order)
                        ->setProduct($faker->randomElement($products))
                        ->setQuantity($faker->numberBetween(1, 5))
                        ->setPrice($faker->randomFloat(2, 10, 200));

                    $manager->persist($orderItem);
                }
            }
        }

        $manager->flush();
    }
}
