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
        $user = $security->getUser();
        if ($user) {
            $rapports = $user->getReports();

            $rapportsArray = [];
            foreach ($rapports as $rapport) {
                $rapportsArray[] = [
                    'github_repository_url' => $rapport->getGithubRepositoryUrl(),
                    'created_at' => $rapport->getCreatedAt()->format('Y-m-d'),
                    'analyse_report' => $rapport->getAnalyseReport(),
                ];
            }

            return $this->render('reports/index.html.twig', [
                'controller_name' => 'ReportHistoryController',
                'rapports' => $rapportsArray
            ]);
        } else {            
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/api/reports', name: 'api_reports')]
    public function Reports(Security $security): Response
    {
        $user = $security->getUser();
        $rapports = $user->getReports();

        $rapportsArray = [];
        foreach ($rapports as $rapport) {
            $analyse_report = $rapport->getAnalyseReport();
            $rapportsArray[] = [
                'github_repository_url' => $rapport->getGithubRepositoryUrl(),
                'created_at' => $rapport->getCreatedAt()->format('Y-m-d H:i:s'),
                'analyse_report' => $analyse_report,
            ];
        }
        return $this->json($rapportsArray);
    }
}
