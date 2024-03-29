<?php

namespace App\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Security $security): Response
    {
        if ($security->getUser()) {
            // Redirige l'utilisateur vers la page d'accueil si connecté
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        } else {
            // Redirige l'utilisateur vers la page de connexion si non connecté
            return $this->redirectToRoute('app_login');
        }
    }
}
