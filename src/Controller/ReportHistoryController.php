<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportHistoryController extends AbstractController
{
    #[Route('/reports', name: 'app_reports')]
    public function index(): Response
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
