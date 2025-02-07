<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Customer;
use App\Entity\LoyaltyCard;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Payment;
use App\Entity\Address;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $users = [];
        $customers = [];
        $categories = [];
        $products = [];
        for ($i = 0; $i < 150; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email)
                ->setPassword(password_hash('password', PASSWORD_BCRYPT))
                ->setRoles(['ROLE_USER'])
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName);
            $manager->persist($user);
            $users[] = $user;
        }
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email)
                ->setPassword(password_hash('password', PASSWORD_BCRYPT))
                ->setRoles(['ROLE_BANNED'])
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName);

            $manager->persist($user);
            $users[] = $user;
        }
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email)
                ->setPassword(password_hash('password', PASSWORD_BCRYPT))
                ->setRoles(['ROLE_ADMIN'])
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName);

            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 0; $i < 5; $i++) {
            $customer = new Customer();
            $customer->setEmail($faker->unique()->email)
                ->setPassword(password_hash('password', PASSWORD_BCRYPT))
                ->setRoles(['ROLE_CUSTOMER'])
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName);

            $loyaltyCard = new LoyaltyCard();
            $loyaltyCard->setCardNumber($faker->uuid)
                ->setCustomer($customer);

            $manager->persist($customer);
            $manager->persist($loyaltyCard);
            $customers[] = $customer;
        }

        // Création des catégories
        $categoryNames = [
            "Portes Classiques", "Portes Mystiques & Secrètes", "Portes Thématiques", "Portes Miniatures", 
            "Portes Célèbres", "Portes de Luxe", "Portes Futuristes", "Portes Étranges", 
            "Portes Animales", "Portes Anciennes"
        ];
        
        foreach ($categoryNames as $categoryName) {
            $category = new Category();
            $category->setName($categoryName)
                ->setDescription($faker->sentence);
            $manager->persist($category);
            $categories[$categoryName] = $category;
        }
        
        $productsData = [
            ["Porte en chêne massif", 250.00, 50, "Portes Classiques"],
            ["Porte secrète bibliothécaire", 500.00, 20, "Portes Mystiques & Secrètes"],
            ["Porte futuriste", 450.00, 15, "Portes Thématiques"],
            ["Mini porte de jardin en bois", 50.00, 100, "Portes Miniatures"],
            ["Réplique de la Porte des étoiles", 1200.00, 5, "Portes Célèbres"],
            ["Porte en verre trempé haut de gamme", 800.00, 10, "Portes de Luxe"],
            ["Porte connectée intelligente", 1000.00, 8, "Portes Futuristes"],
            ["Porte asymétrique design", 600.00, 12, "Portes Étranges"],
            ["Chatière pour animaux en aluminium", 75.00, 30, "Portes Animales"],
            ["Vieille porte restaurée", 400.00, 7, "Portes Anciennes"],
        ];
        
        $productImages = [
            "Porte en chêne massif" => "/assets/images/chene.png",
            "Porte secrète bibliothécaire" => "/assets/images/secrete.png",
            "Porte futuriste" => "/assets/images/futur.png",
            "Mini porte de jardin en bois" => "/assets/images/jardin.png",
            "Réplique de la Porte des étoiles" => "/assets/images/etoiles.png",
            "Porte en verre trempé haut de gamme" => "/assets/images/verre.png",
            "Porte connectée intelligente" => "/assets/images/connectee.png",
            "Porte asymétrique design" => "/assets/images/asymetrique.png",
            "Chatière pour animaux en aluminium" => "/assets/images/animaux.png",
            "Vieille porte restaurée" => "/assets/images/vielle.png"
        ];
        
        foreach ($productsData as [$name, $price, $stock, $categoryName]) {
            $product = new Product();
            $product->setName($name)
                ->setDescription($faker->sentence)
                ->setPrice($price)
                ->setStock($stock)
                ->setCategory($categories[$categoryName])
                ->setPhoto($productImages[$name] ?? "/assets/images/chene.png");
            $manager->persist($product);
            $products[] = $product;
        }
        foreach ($users as $user) {
            $cart = new Cart();
            $cart->setUser($user);
            $manager->persist($cart);

            // Ajout d'articles dans le panier
            for ($j = 0; $j < rand(1, 3); $j++) {
                $cartItem = new CartItem();
                $cartItem->setCart($cart)
                    ->setProduct($products[array_rand($products)])
                    ->setQuantity(rand(1, 5));

                $manager->persist($cartItem);
            }
        }
        foreach ($customers as $customer) {
            $order = new Order();
            $order->setUser($customer)
                ->setStatus('PENDING');
            $manager->persist($order);
            for ($j = 0; $j < rand(1, 3); $j++) {
                $orderItem = new OrderItem();
                $orderItem->setOrder($order)
                    ->setProduct($products[array_rand($products)])
                    ->setQuantity(rand(1, 5))
                    ->setPrice($faker->randomFloat(2, 5, 500));

                $manager->persist($orderItem);
            }
            $payment = new Payment();
            $payment->setOrder($order)
                ->setPaymentMethod('Credit Card')
                ->setAmount($orderItem->getPrice() * $orderItem->getQuantity());
            $manager->persist($payment);
        }
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
        foreach ($customers as $customer) {
            for ($i = 0; $i < 3; $i++) {
                $review = new Review();
                $review->setUser($customer)
                    ->setProduct($products[array_rand($products)])
                    ->setRating(rand(1, 5))
                    ->setComment($faker->sentence);

                $manager->persist($review);
            }
        }

        $manager->flush();
    }
}