<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\UserController;
use App\Repository\UserRepositoryInterface;
use App\Service\UserServiceInterface;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $repository;
    private string $path = '/users/';
    private EntityManagerInterface $entityManager;
    private User $adminUser;
    private User $user;

    protected function setUp(): void
    {
       parent::setUp();
        // Crée un client Symfony
       $this->client = static::createClient();

        // Récupére le gestionnaire d'entités et le référentiel d'utilisateurs à partir du conteneur de services
       $this->entityManager = static::getContainer()->get('doctrine')->getManager();
       $this->userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
        // Initialise le référentiel d'utilisateurs
       $this->repository = $this->entityManager->getRepository(User::class);

       // Supprime toutes les données de la table User avant chaque test
        foreach ($this->repository->findAll() as $user) {
            $this->entityManager->remove($user);
        }

        // Crée un utilisateur avec le rôle d'administrateur
       $adminUser = new User();
       $adminUser->setUsername('admin');
       $adminUser->setEmail('admin@email.fr');
       $adminUser->setPassword(password_hash('password', PASSWORD_DEFAULT));
       $adminUser->setRoles(['ROLE_ADMIN']);

        // Persiste et flush l'utilisateur administrateur
       $this->entityManager->persist($adminUser);
       $this->entityManager->flush();

        // Créer un utilisateur avec le rôle utilisateur
       $user = new User();
       $user->setUsername('user');
       $user->setEmail('user@email.fr');
       $user->setPassword(password_hash('password', PASSWORD_DEFAULT));
       $user->setRoles(['ROLE_USER']);

        // Persiste et flush l'utilisateur
       $this->entityManager->persist($user);
       $this->entityManager->flush();

        // Affecter $adminUser à la variable de classe pour qu'elle soit accessible dans d'autres méthodes
       $this->adminUser = $adminUser;
       $this->user = $user;
   }

   // Test d'affichage de la liste des utilisateurs
    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        // On s'assure que la redirection est suivie
        self::assertTrue($this->client->getResponse()->isRedirect());

        // On suit la redirection et vérifie la réponse
        $crawler = $this->client->followRedirect();

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('To Do List app');
    }

    // Test de la création d'utilisateur avec un utilisateur administrateur
    public function testNewUserWithAdminRole(): void
    {
        // On vérifie si un user avec le rôle admin existe
        $this->client->loginUser($this->adminUser);

        // Assure que l'utilisateur administrateur existe
        self::assertNotNull($this->adminUser);

        // Accéde à la page de création d'utilisateur
        $crawler = $this->client->request('GET', '/users/create');

        // Vérifie que la page est accessible
       self::assertResponseIsSuccessful();

        // Soumetle formulaire de création d'utilisateur
        $this->client->submitForm('Save', [
            'user[username]' => 'Testing',
            'user[password][first]' => 'Testing',
            'user[password][second]' => 'Testing',
            'user[email]' => 'test@mail.fr',
            'user[roles]' => ['ROLE_USER'],
        ]);

        // Vérifie que la redirection vers la liste des utilisateurs est effectuée
        self::assertResponseRedirects('/users/');

        // Vérifie que l'utilisateur a bien été ajouté en base de données
        $user = $this->userRepository->findOneBy(['username' => 'Testing']);
        self::assertNotNull($user);
    }

     // Test de la création d'utilisateur avec un utilisateur classique
    public function testNewUserWithUserRole(): void
    {
        // Connecte l'utilisateur avec le rôle ROLE_USER
        $this->client->loginUser($this->user);

        // Assure que l'utilisateur existe
        self::assertNotNull($this->user);

        // Accéde à la page de création d'utilisateur
        $crawler = $this->client->request('GET', '/users/create');

        // Vérifie que la réponse est une redirection (statut HTTP 302)
        self::assertTrue($this->client->getResponse()->isRedirect());

        // Suivre la redirection et vérifier la route ou l'URL de la page d'accueil
        $crawler = $this->client->followRedirect();
        self::assertRouteSame('homepage');

        // Vérifie que l'utilisateur n'a pas accès au formulaire de création d'utilisateur
        self::assertSelectorNotExists('form[name="user"]'); 
    }

    // Test de la modification d'utilisateur avec un utilisateur administrateur
    public function testEditUserWithAdminRole(): void
    {
        // Connecte l'utilisateur administrateur
        $this->client->loginUser($this->adminUser);

        // Accéde à la page d'édition de l'utilisateur "user"
        $userId = $this->user->getId();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $userId));

        // Vérifie si la page d'édition a été chargée correctement
        self::assertResponseIsSuccessful();

        // Remplit et soumet le formulaire d'édition
        $this->client->submitForm('Modifier', [
            'user[username]' => 'Something New',
            'user[password][first]' => 'newpassword',
            'user[password][second]' => 'newpassword',
            'user[email]' => 'newemail@email.fr',
            'user[roles]' => ['ROLE_USER'], 
        ]);

        // Mise à jour de la base de données
        $this->entityManager->flush();

        // Vérifie que la redirection vers la liste des utilisateurs est effectuée
        self::assertResponseRedirects('/users/');

        // Récupére l'utilisateur après l'édition depuis la base de données
        $updatedUser = $this->repository->find($userId);
    }

    // Test de la modification d'utilisateur avec un utilisateur classique
    public function testEditUserWithUserRole(): void
    {
        // Connecte l'utilisateur avec le rôle ROLE_USER
        $this->client->loginUser($this->user);

        // Tente d'accéder à la page d'édition de l'utilisateur "admin"
        $userId = $this->adminUser->getId();
        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $userId));

        // Vérifie si l'utilisateur est redirigé
        self::assertTrue($this->client->getResponse()->isRedirect());

        // Suit la redirection et vérifie la route ou l'URL de la page vers laquelle l'utilisateur est redirigé
        $crawler = $this->client->followRedirect();
        self::assertRouteSame('homepage');

        // Vérifie que l'utilisateur n'a pas accès au formulaire de modification de l'utilisateur
        self::assertSelectorNotExists('form[name="user"]');
    }
}