<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class SecurityControllerTest extends WebTestCase
{
    // Test de l'authentification
    public function testLoginPage()
    {
        $client = static::createClient();

        // Accéde à la page de connexion
        $crawler = $client->request('GET', '/login');

        // Vérifie que la page est accessible
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');

        // Remplit et soumet le formulaire de connexion
        $form = $crawler->selectButton('Se connecter')->form();
        $form['username'] = 'your_username';
        $form['password'] = 'your_password';
        $client->submit($form);

        // Vérifie que l'utilisateur est redirigé après une connexion réussie
        $this->assertResponseRedirects('/login');

        // Suit la redirection après la connexion
        $client->followRedirect();

        // Vérifie que l'utilisateur est bien connecté, en vérifiant un élément de la page après connexion
        $this->assertSelectorTextContains('h1', 'Connexion'); 
    }
}
