<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/tasks')]
class TaskController extends AbstractController
{

    // Stock l'instance de l'interface d'authentification
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    // Affiche la liste des tâches
    #[Route('/', name: 'task_list', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    // Enregistre un nouvelle tâche
    #[Route('/create', name: 'task_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SecurityController $lastUsername, SessionInterface $session): Response
    {
        // On réccupère la session de l'utilisateur
        $user = $this->security->getUser();

        // On vérifie si l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('homepage'); 
        }

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            // Si l'utilisateur est connecté, il devient l'auteur de la tâche
            if ($user) {
                $task->setAuthor($user);
            } else {
                // Si l'utilisateur n'est pas connecté, on attribue la tâche à l'utilisateur 'Anonyme'
                $anonymeUser = $entityManager->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
                if ($anonymeUser) {
                    $task->setAuthor($anonymeUser);
                }
            }
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/create.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    // Modifie une tâche
    #[Route('/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager, SecurityController $lastUsername, SessionInterface $session): Response
    {
        // On réccupère la session de l'utilisateur
        $user = $this->security->getUser();

        // On vérifie si l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('homepage'); 
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    // Marque la tâche comme étant effectuée
    #[Route('/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager): Response
    {
        $task->toggle(!$task->isDone());
        $entityManager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    // Suppression d'une tâche
    #[Route('/{id}/delete', name: 'task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); 

        // Vérifie si l'utilisateur actuel est l'auteur de la tâche
        if (!($user === $task->getAuthor() || (in_array('ROLE_ADMIN', $user->getRoles(), true) && $task->getAuthor() === null))) {
            $this->addFlash('error', 'Vous n\'avez pas les droits suffisants pour supprimer cette tâche.');
            return $this->redirectToRoute('homepage');
        }

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
    }
}
