<?php 
namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserRoles(): void
    {
        $user = new User();
        
        // Vérifie que le rôle par défaut est ROLE_USER
        $this->assertContains('ROLE_USER', $user->getRoles());

        // Ajoute un rôle et vérifie qu'il est bien ajouté
        $user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());

        // Vérifie que le rôle ROLE_USER est toujours présent
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}
