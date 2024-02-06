<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(Security $security): Response
    {
        if ($security->getUser()) {
            // Redirige l'utilisateur vers la page d'accueil si connecté
            return $this->redirectToRoute('app_home');
        } else {
            // Redirige l'utilisateur vers la page de connexion si non connecté
            
            return $this->render('login/index.html.twig', [
                'controller_name' => 'LoginController',
            ]);
        }
    }
}
