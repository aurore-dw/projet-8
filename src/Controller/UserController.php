<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[Route('/users')]
class UserController extends AbstractController
{
    // Stock l'instance de l'interface d'authentification
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    // Affiche la liste des utilisateurs
    #[Route('/', name: 'user_list', methods: ['GET'])]
    public function index(UserRepository $userRepository, SecurityController $lastUsername, SessionInterface $session): Response
    {
        // On réccupère la session de l'utilisateur
        $user = $this->security->getUser();

        // On vérifie si l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('homepage'); 
        }

        // Si l'utilisateur est connecté, on vérifie s'il a un rôle administrateur
        if (!in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $this->addFlash('error', 'Vous n\'avez pas les droits suffisants pour accéder à cette page.');
            return $this->redirectToRoute('homepage');
        }
        
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    // Créer un nouvel utilisateur
    #[Route('/create', name: 'user_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, SecurityController $lastUsername, SessionInterface $session): Response
    {
        // On réccupère la session de l'utilisateur
        $user = $this->security->getUser();

        // On vérifie si l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('homepage'); 
        }

        // Si l'utilisateur est connecté, on vérifie s'il a un rôle administrateur
        if (!in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $this->addFlash('error', 'Vous n\'avez pas les droits suffisants pour accéder à cette page.');
            return $this->redirectToRoute('homepage');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupére les rôles sélectionnés
            $selectedRoles = $form->get('roles')->getData();
            // On vérifie si $selectedRoles est un tableau
            $selectedRoles = is_array($selectedRoles) ? $selectedRoles : [$selectedRoles];
            // Assigne le rôle choisi à l'utilisateur
            $user->setRoles($selectedRoles);
            // Récupére le mot de passe en clair
            $plainPassword = $form->get('password')->getData();
            // Hashe le mot de passe
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
            // Défini le mot de passe hashé sur l'objet User
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/create.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    // Modifie les données de l'utilisateur
    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, User $user, EntityManagerInterface $entityManager, SecurityController $lastUsername, SessionInterface $session): Response
    {
        // On réccupère la session de l'utilisateur
        $user = $this->security->getUser();

        // On vérifie si l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('homepage'); 
        }

        // Si l'utilisateur est connecté, on vérifie s'il a un rôle administrateur
        if (!in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $this->addFlash('error', 'Vous n\'avez pas les droits suffisants pour accéder à cette page.');
            return $this->redirectToRoute('homepage');
        }
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupére les rôles sélectionnés
            $selectedRoles = $form->get('roles')->getData();
            // On vérifie si $selectedRoles est un tableau
            $selectedRoles = is_array($selectedRoles) ? $selectedRoles : [$selectedRoles];
            // Assigne le rôle choisi à l'utilisateur
            $user->setRoles($selectedRoles);

            $entityManager->flush();

            return $this->redirectToRoute('user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

}
