<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Security $security): Response
    {
        if ($security->getUser()) {
            return $this->redirectToRoute('app_home');
        } else {            
            return $this->render('register/index.html.twig', [
                'controller_name' => 'RegisterController',
            ]);
        }
    }
}
