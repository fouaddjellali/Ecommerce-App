<?php 

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminAccessTest extends WebTestCase
{
    public function testAdminPageRequiresLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin');

        $this->assertResponseRedirects('/login');
    }

    public function testAdminAccessAsAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }

    private function createAdminUser()
    {
        $user = new \App\Entity\User();
        $user->setEmail('admin@test.com');
        $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
        $user->setRoles(['ROLE_ADMIN']);

        return $user;
    }
}