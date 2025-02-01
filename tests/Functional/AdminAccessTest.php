<?php 
namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminAccessTest extends WebTestCase
{
public function testAdminPageRequiresLogin(): void
{
$client = static::createClient();

// Essayer d'accéder à /admin sans être connecté
$client->request('GET', '/admin');

// Vérifier si on est redirigé vers la page de login (statut 302)
$this->assertResponseRedirects('/login');
}

public function testAdminAccessAsAdmin(): void
{
$client = static::createClient();
$client->loginUser($this->createAdminUser());

// Essayer d'accéder à /admin
$client->request('GET', '/admin');

// Vérifier si la réponse est OK (statut 200)
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