<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Game;
use App\Exception\CacheException;

class GameService
{
    private const GAME_CACHE_KEY_PREFIX = 'Game_Key_';
    private const GAME_PERSIST_ADD_ACTION = 'add';
    private const GAME_PERSIST_UPDATE_ACTION = 'update';

    public function __construct(private readonly CacheService $cacheService)
    {
    }

    public function addGame(Game $game): bool
    {
        return $this->persistGame($game, self::GAME_PERSIST_ADD_ACTION);
    }

    public function updateGame(Game $game): bool
    {
        return $this->persistGame($game, self::GAME_PERSIST_UPDATE_ACTION);
    }

    /**
     * @throws CacheException
     */
    public function finishGame(string $homeTeam, string $awayTeam): void
    {
        $this->updateIsFinished($homeTeam, $awayTeam,true);
    }

    /**
     * @throws CacheException
     */
    public function startGame(string $homeTeam, string $awayTeam): void
    {
        $this->updateIsFinished($homeTeam, $awayTeam,false);
    }

    /**
     * @throws CacheException
     */
    public function fetchGame(string $homeTeam, string $awayTeam): Game
    {
        $cacheKey = $this->buildCacheKey($homeTeam, $awayTeam);

        return $this->cacheService->fetch($cacheKey);
    }

    public function getGamesList(): array
    {
        try {
            $cacheKeys = $this->extractKeysList($this->cacheService->getAllKeys());
            $games = $this->cacheService->fetchAll($cacheKeys);

            usort($games,[$this, 'sortGamesList']);

            return $games;
        } catch (CacheException $exception) {
            return [];
        }
    }

    private function sortGamesList(Game $gameOne, Game $gameTwo): int
    {
        $gameOneTime = $gameOne->getCreatedAt()->getTimestamp();
        $gameTwoTime = $gameTwo->getCreatedAt()->getTimestamp();
        $diff =  $gameTwo->getTotalScore() - $gameOne->getTotalScore();

        return ($diff !== 0) ? $diff :  $gameTwoTime - $gameOneTime;
    }

    private function extractKeysList(array $cacheKeys): array
    {
        $keysList = [];
        foreach ($cacheKeys as $prefixedCacheKey) {
            if (str_contains($prefixedCacheKey, self::GAME_CACHE_KEY_PREFIX)) {
                $cacheKeyParts = explode(':', $prefixedCacheKey);
                $keysList[] = $cacheKeyParts[1] ?? null;
            }
        }

        return $keysList;
    }

    /**
     * @throws CacheException
     */
    private function updateIsFinished(string $homeTeam, string $awayTeam, bool $status): void
    {
        $cacheKey = $this->buildCacheKey($homeTeam, $awayTeam);
        $game = $this->cacheService->fetch($cacheKey);
        $game->setIsFinished($status);
        $this->cacheService->persist($cacheKey, $game);
    }

    private function persistGame(Game $game, string $action): bool
    {
        try {
            $cacheKey = $this->buildCacheKey($game->getHomeTeam(), $game->getAwayTeam());
            $exists = $this->cacheService->fetch($cacheKey);
            if ($exists && $action == self::GAME_PERSIST_ADD_ACTION) {
                return true;
            }
            if (!$exists && $action == self::GAME_PERSIST_UPDATE_ACTION) {
               return false;
            }

            $game->setTotalScore($game->getHomeTeamScore() + $game->getAwayTeamScore());
            $this->cacheService->persist($cacheKey, $game);

            return true;
        } catch (CacheException $exception) {
            return false;
        }
    }

    private function buildCacheKey(string $keyPartOne, string $keyPartTwo): string
    {
        return self::GAME_CACHE_KEY_PREFIX . $keyPartOne . '_' . $keyPartTwo;
    }
}
