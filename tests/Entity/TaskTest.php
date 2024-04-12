<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGettersAndSetters()
    {
        // Créer une instance de la classe User
        $user = new User();

        // Créer une instance de la classe Task
        $task = new Task();

        // Définir des valeurs pour les propriétés
        $task->setTitle('title');
        $task->setContent('content');
        $date = new \DateTimeImmutable();
        $task->setCreatedAt($date);

        // Définir l'utilisateur en tant qu'auteur de la tâche
        $task->setAuthor($user);

        // Vérifier que les valeurs sont correctement récupérées
        $this->assertEquals('title', $task->getTitle());
        $this->assertEquals('content', $task->getContent());
        $this->assertEquals($date, $task->getCreatedAt());
        $this->assertEquals($user, $task->getAuthor());
    }
}


