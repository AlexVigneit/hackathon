<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class ReportHistoryController extends AbstractController
{
    #[Route('/reports', name: 'app_reports')]
    public function index(Security $security): Response
    {
        if ($security->getUser()) {
            return $this->render('reports/index.html.twig', [
                'controller_name' => 'ReportHistoryController',
            ]);
        } else {            
            return $this->redirectToRoute('app_login');
        }
    }
}
