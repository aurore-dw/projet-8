<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Annotation\Route;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TaskRepository $repository;
    private string $path = '/tasks/';
    private EntityManagerInterface $entityManager;
    private User $adminUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Task::class);
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        // Supprime toutes les données de la table Task avant chaque test
        foreach ($this->repository->findAll() as $object) {
            $this->entityManager->remove($object);
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

       // Affecte l'utilisateur administrateur
       $this->adminUser = $adminUser;
       // Affecte l'utilisateur
       $this->user = $user;
    }

    // Teste de l'affichage de la liste des tâches
    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('To Do List app');
    }

    // Test de la création d'une tâche avec le rôle administrateur
    public function testNewTaskWithAdminRole(): void
    {
        // On vérifie si un user avec le rôle admin existe
        $this->client->loginUser($this->adminUser);

        // Assure que l'utilisateur administrateur existe
        self::assertNotNull($this->adminUser);

        // Compte le nombre d'objets dans le repository avant la création
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%screate', $this->path));

        // Vérifie que la page est accessible
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Ajouter', [
            'task[title]' => 'Test Title',
            'task[content]' => 'Test Content',
        ]);

        self::assertResponseRedirects('/tasks/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    // Test de la création de tâche avec le rôle user
    public function testNewTaskWithUserRole(): void
    {
        // Connecte l'utilisateur avec le rôle USER
        $this->client->loginUser($this->user);

        // Assure que l'utilisateur existe
        self::assertNotNull($this->user);

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%screate', $this->path));

        // Vérifie que la page est accessible
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Ajouter', [
            'task[title]' => 'Test Title',
            'task[content]' => 'Test Content',
        ]);

        // Vérifie que l'utilisateur est redirigé vers la page d'accueil
        self::assertResponseRedirects('/tasks/');
    }

    // Test de la modification d'une tâche avec le rôle admininistrateur
    public function testEditTaskWithAdminRole(): void
    {
        // Connecte l'utilisateur administrateur
        $this->client->loginUser($this->adminUser);

        // Assure que l'utilisateur administrateur existe
        self::assertNotNull($this->adminUser);

        // Crée une nouvelle tâche à modifier
        $fixture = new Task();
        $fixture->setCreatedAt(new \DateTimeImmutable()); 
        $fixture->setTitle('My Title');
        $fixture->setContent('My Content');
        $fixture->IsDone(true);

        // Enregistre la tâche en BDD
        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        // Réccupère la tâche pour la modification
        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        //
        $this->client->submitForm('Modifier', [
            'task[title]' => 'Something New',
            'task[content]' => 'Something New',
        ]);

        self::assertResponseRedirects('/tasks/');

        $fixture = $this->repository->findAll();

        self::assertInstanceOf(\DateTimeImmutable::class, $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getContent());
    }

    // Test de la modification d'une tâche avec le rôle user
    public function testEditTaskWithUserRole(): void
    {
        // Connecte l'utilisateur administrateur
        $this->client->loginUser($this->user);

        // Assure que l'utilisateur administrateur existe
        self::assertNotNull($this->user);

        // Crée une nouvelle tâche à modifier
        $fixture = new Task();
        $fixture->setCreatedAt(new \DateTimeImmutable()); 
        $fixture->setTitle('My Title');
        $fixture->setContent('My Content');
        $fixture->IsDone(true);

        // Enregistre la tâche en BDD
        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Modifier', [
            'task[title]' => 'Something New',
            'task[content]' => 'Something New',
        ]);

        self::assertResponseRedirects('/tasks/');

        $fixture = $this->repository->findAll();

        self::assertInstanceOf(\DateTimeImmutable::class, $fixture[0]->getCreatedAt()); 
        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getContent());
    }

    // Test d'un utilisateur non connecté tentant d'accéder au formulaire de création de tâche
    public function testNewTaskForUnauthorizedUser(): void
    {
        // Accéde à la page de création de tâche sans être connecté
        $crawler = $this->client->request('GET', $this->path . 'create');

        // Vérifie que l'utilisateur est redirigé vers la page d'authentification
        self::assertResponseRedirects('/');
    }

}
