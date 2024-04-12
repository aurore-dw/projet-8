<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    // Test de l'affichage de la page d'accueil
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }
}