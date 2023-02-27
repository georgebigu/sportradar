<?php

declare(strict_types=1);

namespace App\Entity;

class Game
{
    public function __construct(
        private string $homeTeam = '',
        private string $awayTeam = '',
        private int $homeTeamScore = 0,
        private int $awayTeamScore = 0,
        private bool $isFinished = false,
        private int $totalScore = 0,
        private \DateTime $createdAt = new \DateTime()
    ) {
    }

    public function getHomeTeam(): string
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(string $homeTeam): Game
    {
        $this->homeTeam = $homeTeam;
        return $this;
    }

    public function setAwayTeam(string $awayTeam): Game
    {
        $this->awayTeam = $awayTeam;
        return $this;
    }

    public function getAwayTeam(): string
    {
        return $this->awayTeam;
    }

    public function getHomeTeamScore(): int
    {
        return $this->homeTeamScore;
    }

    public function setHomeTeamScore(int $homeTeamScore): Game
    {
        $this->homeTeamScore = $homeTeamScore;
        return $this;
    }

    public function getAwayTeamScore(): int
    {
        return $this->awayTeamScore;
    }

    public function setAwayTeamScore(int $awayTeamScore): Game
    {
        $this->awayTeamScore = $awayTeamScore;
        return $this;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): Game
    {
        $this->isFinished = $isFinished;
        return $this;
    }

    public function getTotalScore(): int
    {
        return $this->totalScore;
    }

    public function setTotalScore(int $totalScore): Game
    {
        $this->totalScore = $totalScore;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): Game
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
