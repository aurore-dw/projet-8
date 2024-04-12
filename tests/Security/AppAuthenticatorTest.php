<?php

namespace App\Tests\Security;

use App\Security\AppAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticatorTest extends TestCase
{
    public function testAuthenticate()
    {
        // Crée un mock pour UrlGeneratorInterface
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        
        // Crée une nouvelle requête
        $request = new Request();
        
        // Crée un mock pour SessionInterface et définit la session dans la requête
        $session = $this->createMock(SessionInterface::class);
        $request->setSession($session); // Définir la session
        
        // Crée une instance de AppAuthenticator avec le mock de UrlGeneratorInterface
        $authenticator = new AppAuthenticator($urlGenerator);
        
        // Simule une requête POST avec des données d'authentification
        $request->request->set('username', 'testuser');
        $request->request->set('password', 'testpassword');
        $request->request->set('_csrf_token', 'testcsrftoken');
        
        // Appele la méthode authenticate() de l'authenticator
        $passport = $authenticator->authenticate($request);
        
        // Vérifie que le résultat de la méthode authenticate() est un objet Passport
        $this->assertInstanceOf(Passport::class, $passport);
        
        // Vérifie que le Passport contient les badges nécessaires
        $this->assertCount(3, $passport->getBadges());
        $this->assertInstanceOf(UserBadge::class, $passport->getBadge(UserBadge::class));
        $this->assertInstanceOf(PasswordCredentials::class, $passport->getBadge(PasswordCredentials::class));
        $this->assertInstanceOf(CsrfTokenBadge::class, $passport->getBadge(CsrfTokenBadge::class));
    }

    public function testOnAuthenticationSuccess()
    {
        // Crée un mock pour UrlGeneratorInterface
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        
        // Définit le comportement attendu du mock pour la méthode generate()
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('homepage')
            ->willReturn('/'); // Supposons que l'URL de la page d'accueil est '/'
        
        // Crée une instance de AppAuthenticator avec le mock de UrlGeneratorInterface
        $authenticator = new AppAuthenticator($urlGenerator);

        // Crée une nouvelle requête
        $request = new Request();
        
        // Crée un mock pour SessionInterface et définir la session dans la requête
        $session = $this->createMock(SessionInterface::class);
        $request->setSession($session); // Définir la session

        // Crée un mock pour TokenInterface
        $token = $this->createMock(TokenInterface::class);
        
        // Appele la méthode onAuthenticationSuccess() de l'authenticator
        $response = $authenticator->onAuthenticationSuccess($request, $token, 'firewallName');

        // Vérifir que le résultat de la méthode est une instance de Response
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGetLoginUrl()
    {
        // Crée un mock pour UrlGeneratorInterface
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        
        // Défini le comportement attendu du mock pour la méthode generate()
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('homepage') // Utiliser 'homepage' comme attendu
            ->willReturn('/'); // Supposons que l'URL de la page d'accueil est '/homepage'
        
        // Crée une instance de AppAuthenticator avec le mock de UrlGeneratorInterface
        $authenticator = new AppAuthenticator($urlGenerator);

        // Crée une nouvelle requête
        $request = new Request();
        
        // Crée un mock pour SessionInterface et définir la session dans la requête
        $session = $this->createMock(SessionInterface::class);
        $request->setSession($session); // Définir la session
        
        // Crée un mock pour TokenInterface
        $token = $this->createMock(TokenInterface::class);
        
        // Appele la méthode onAuthenticationSuccess() de l'authenticator
        $response = $authenticator->onAuthenticationSuccess($request, $token, 'firewallName');

        // Vérifie que le résultat de la méthode est une instance de Response
        $this->assertInstanceOf(Response::class, $response);
    }
}
