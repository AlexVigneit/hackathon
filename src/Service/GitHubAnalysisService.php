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
        $this->phpFilesDirectory = $_SERVER['DOCUMENT_ROOT'] . 'phpFiles';
        if (!file_exists($this->phpFilesDirectory)) {
            mkdir($this->phpFilesDirectory, 0777, true);
        }
    }

    public function processAnalysisRequest($url): string
    {
        [$owner, $repo] = $this->parseRepositoryUrl($url);


        try {
            $files = $this->getPhpFiles($owner, $repo);
            foreach ($files as $file) {
                $content = $this->getFileContent($owner, $repo, $file['path']);
                file_put_contents($this->phpFilesDirectory . '/' . basename($file['path']), $content);
            }
        } catch (GuzzleException $e) {
            throw new HttpException(500, 'error finding file : ' . $e->getMessage());
        }

        return $this->runPhpStan();
    }

    private function parseRepositoryUrl(string $repositoryUrl): array
    {
        $parts = explode('/', trim(parse_url($repositoryUrl, PHP_URL_PATH), '/'));
        if (count($parts) !== 2) {
            throw new HttpException(400, 'invalid repository url');
        }

        return $parts;
    }

    private function getPhpFiles(string $owner, string $repo): array
    {
        $repoInfo = $this->client->request('GET', "https://api.github.com/repos/$owner/$repo");
        $defaultBranch = json_decode($repoInfo->getBody())->default_branch;
        $response = $this->client->request('GET', "https://api.github.com/repos/$owner/$repo/git/trees/$defaultBranch?recursive=1");
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
        error_log($this->phpFilesDirectory);
        $reportString = "<style>.report-content {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
          }
          .report-title {
            margin-bottom: 20px;
        }
        
        .file-heading {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .error-message {
            margin-bottom: 12px;
        }
        .wrapper-file{
          background-color: #ced4da;
        }</style><h3 class='report-title'>Analyze report :</h3>";
        foreach ($reportData['files'] as $file => $errors) {
            $file = str_replace($this->phpFilesDirectory, '', $file);
            $reportString.= "<div class='wrapper-file'>";
            $reportString .= "<div class='file-heading'>File : $file</div>";
            foreach ($errors['messages'] as $error) {
                $reportString .= "<div class='error-message'>Line {$error['line']} : {$error['message']}</div>";
            }
            $reportString.= "</div>";
        }

        return $reportString;
    }
}
