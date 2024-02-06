<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportHistoryController extends AbstractController
{
    #[Route('/report/history', name: 'app_report_history')]
    public function index(): Response
    {
        return $this->render('report_history/index.html.twig', [
            'controller_name' => 'ReportHistoryController',
        ]);
    }
}
