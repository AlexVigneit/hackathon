<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Entity\Report;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GitHubAnalysisService
{
    private $client;
    private $phpFilesDirectory;
    
    public function __construct()
    {
        $this->client = new Client();
        $this->phpFilesDirectory = 'Applications/MAMP/htdocs/hackathon-security/backend/src/phpFiles';
        if (!file_exists($this->phpFilesDirectory)) {
            mkdir($this->phpFilesDirectory, 0777, true);
        }
    }

    public function processAnalysisRequest($url) : string
    {
        [$owner, $repo] = $this->parseRepositoryUrl($url);

        try {
            $files = $this->getPhpFiles($owner, $repo);
            foreach ($files as $file) {
                $content = $this->getFileContent($owner, $repo, $file['path']);
                file_put_contents($this->phpFilesDirectory . '/' . basename($file['path']), $content);
            }
        } catch (GuzzleException $e) {
            throw new HttpException(500, 'Erreur lors de la récupération des fichiers : ' . $e->getMessage());
        }

        return $this->runPhpStan();

    }

    private function parseRepositoryUrl(string $repositoryUrl): array
    {
        $parts = explode('/', trim(parse_url($repositoryUrl, PHP_URL_PATH), '/'));
        if (count($parts) !== 2) {
            throw new HttpException(400, 'URL du dépôt GitHub non valide');
        }

        return $parts;
    }

    private function getPhpFiles(string $owner, string $repo): array
    {
        $response = $this->client->request('GET', "https://api.github.com/repos/$owner/$repo/git/trees/master?recursive=1");
        $data = json_decode($response->getBody(), true);

        return array_filter($data['tree'], function ($item) {
            return str_ends_with($item['path'], '.php');
        });
    }

    private function getFileContent(string $owner, string $repo, string $path): string
    {
        $response = $this->client->request('GET', "https://raw.githubusercontent.com/$owner/$repo/master/$path");
        return (string)$response->getBody();
    }

    private function runPhpStan(): string
    {
        exec($_SERVER['DOCUMENT_ROOT'] . '/../vendor/bin/phpstan analyse ' . $this->phpFilesDirectory . ' --level=max --error-format=json > phpstan-report.json');

        // Lire le fichier de rapport
        $reportJson = file_get_contents('phpstan-report.json');
        $reportData = json_decode($reportJson, true);
        // Convertir le rapport JSON en chaîne de caractères lisible
        $reportString = $this->formatReport($reportData);
        $files = glob($this->phpFilesDirectory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            } elseif (is_dir($file)) {
                removeDirectory($file);
            }
        }
        rmdir($this->phpFilesDirectory);
        return $reportString;
    }

    private function formatReport(array $reportData): string
    {
        $basePathToRemove = "/Applications/MAMP/htdocs/hackathon-security/backend/public/Applications/MAMP/htdocs/hackathon-security/backend/src/phpFiles/";
        $reportString = "Rapport d'analyse :\n";
        foreach ($reportData['files'] as $file => $errors) {
            $file = str_replace($basePathToRemove, '', $file);
            $reportString .= "\nFichier : $file\n";
            foreach ($errors['messages'] as $error) {
                $reportString .= "Ligne {$error['line']} : {$error['message']}\n";
            }
        }
        return $reportString;
    }
}
