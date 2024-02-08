<?php

namespace App\Controller;

use App\Entity\Report;
use App\Service\GitHubAnalysisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnalyseController extends AbstractController
{
    #[Route('/analyse', name: 'analyse_url', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        $mail = $user->getUserIdentifier();
        $data = json_decode($request->getContent(), true);
        $url = $data['url'] ?? null;
        $analysisRequest = new Report();
        $gitHubAnalysisService = new GitHubAnalysisService();
        $report = $gitHubAnalysisService->processAnalysisRequest($url);

        try {
            $report = $gitHubAnalysisService->processAnalysisRequest($url);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Une erreur est survenue. Vérifiez que le repo est Public et qu\'il comporte bien des fichiers écrits en PHP.');
        }


        $analysisRequest->setGithubRepositoryUrl($url);
        $analysisRequest->setCreatedAt(new \DateTimeImmutable());
        $analysisRequest->setAnalyseReport($report);
        $analysisRequest->setUser($user);


        $entityManager->persist($analysisRequest);
        $entityManager->flush();

        $user->addReport($analysisRequest);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'report' => $report,
            'email' => $mail
        ]);
    }
}
