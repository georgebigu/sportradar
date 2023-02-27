<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Game;
use App\Exception\CacheException;

class GameService
{
    private const GAME_CACHE_KEY_PREFIX = 'Game_Key_';

    public function __construct(private readonly CacheService $cacheService)
    {
    }

    public function addGame(Game $game): bool
    {
        try {
            $cacheKey = self::GAME_CACHE_KEY_PREFIX . $game->getHomeTeam() . '_' . $game->getAwayTeam();
            $exists = $this->cacheService->fetch($cacheKey);

            if ($exists) {
                return true;
            }


            $this->cacheService->persist($cacheKey, $game);

            return true;
        } catch (CacheException $exception) {
            return false;
        }
    }

    public function updateGame(Game $game): bool
    {
        return true;
    }

    public function getGamesList(): array
    {
        $cacheKeys = $this->cacheService->getAllKeys();
        $keysList = [];
        foreach ($cacheKeys as $prefixedCacheKey) {
            if (str_contains($prefixedCacheKey, self::GAME_CACHE_KEY_PREFIX)) {
                $cacheKeyParts = explode(':', $prefixedCacheKey);
                $keysList[] = $cacheKeyParts[1] ?? null;
            }
        }

        return $this->cacheService->fetchAll($keysList);
    }
}
