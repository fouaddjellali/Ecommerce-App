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
        for ($i = 0; $i < 3; $i++) {
            $category = new Category();
            $category->setName($faker->word)
                ->setDescription($faker->sentence);

            $manager->persist($category);
            $categories[] = $category;
        }
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($faker->word)
                ->setDescription($faker->sentence)
                ->setPrice($faker->randomFloat(2, 5, 500))
                ->setStock($faker->numberBetween(10, 100))
                ->setCategory($categories[array_rand($categories)]);
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
