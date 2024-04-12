<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        // Créer plusieurs utilisateurs avec le rôle ROLE_USER
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setUsername('user' . $i);
            $user->setEmail('user' . $i . '@email.com');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(password_hash('1234', PASSWORD_DEFAULT));
            $manager->persist($user);
        }

         // Créer plusieurs utilisateurs avec le rôle ROLE_ADMIN
        for ($i = 0; $i < 3; $i++) {
            $admin = new User();
            $admin->setUsername('admin' . $i);
            $admin->setEmail('admin' . $i . '@email.com');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword(password_hash('1234', PASSWORD_DEFAULT)); 
            $manager->persist($admin);
        }

        // Créer un utilisateur avec le rôle ROLE_ANONYME
        $user = new User();
        $user->setUsername('Anonyme');
        $user->setEmail('anonyme@email.com');
        $user->setRoles(['ROLE_ANONYME']);
        $user->setPassword(password_hash('1234', PASSWORD_DEFAULT)); 
        $manager->persist($user);
        
        // Création de tâches avec des auteurs aléatoires parmi les utilisateurs générés par les fixtures
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setTitle($faker->sentence(2)); // Génère un titre de 2 mots
            $task->setContent($faker->sentence(3)); // Génère un contenu de 3 mots
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->toggle($faker->boolean); // Génère une valeur booléenne aléatoire (true/false)
            $manager->persist($task);
        }
        // Enregistre les utilisateurs dans la base de données
        $manager->flush();
    }
}
