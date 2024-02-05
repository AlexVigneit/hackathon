<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $github_repository_url = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $analyse_report = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGithubRepositoryUrl(): ?string
    {
        return $this->github_repository_url;
    }

    public function setGithubRepositoryUrl(string $github_repository_url): static
    {
        $this->github_repository_url = $github_repository_url;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAnalyseReport(): ?string
    {
        return $this->analyse_report;
    }

    public function setAnalyseReport(string $analyse_report): static
    {
        $this->analyse_report = $analyse_report;

        return $this;
    }
}
