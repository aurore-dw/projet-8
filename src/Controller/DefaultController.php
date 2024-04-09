<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DefaultController extends AbstractController
{   
    // Page d'accueil
    #[Route('/', name: 'homepage')]
    public function index(SessionInterface $session)
    {
        $errorMessage = $session->getFlashBag()->get('danger', []);

        return $this->render('index.html.twig', [
        'errorMessage' => $errorMessage,
        ]);
    }
}
