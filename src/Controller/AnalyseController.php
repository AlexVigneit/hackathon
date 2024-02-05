<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GitHubAnalysisService;

class AnalyseController extends AbstractController
{
    #[Route('/analyse', name: 'analyse_url', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $url = $data['url'] ?? null;
        $analysisRequest = new Report();
        $gitHubAnalysisService = new GitHubAnalysisService();
        $report = $gitHubAnalysisService->processAnalysisRequest($url);
        $analysisRequest->setGithubRepositoryUrl($url);
        $analysisRequest->setCreatedAt(new \DateTimeImmutable());
        $analysisRequest->setAnalyseReport($report);

        $entityManager->persist($analysisRequest);
        $entityManager->flush();

        return $this->json($analysisRequest);
    }
}
