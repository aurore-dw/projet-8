<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

	public function testGettersAndSetters()
    {
    	//Créer une instance de la classe User
    	$user = new User();

    	// Définit des valeurs pour les propriétés
        $user->setUsername('john_doe');
        $user->setPassword('password123');
        $user->setEmail('john.doe@example.com');

        // Vérifie que les valeurs sont correctement récupérées
        $this->assertEquals('john_doe', $user->getUsername());
        $this->assertEquals('password123', $user->getPassword());
        $this->assertEquals('john.doe@example.com', $user->getEmail());

    }

}