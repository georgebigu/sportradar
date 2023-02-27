<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

class CacheService
{
    public function __construct(
        private readonly CacheInterface $cacheAdapter,
        private readonly LoggerInterface $logger
    ) {
    }

    public function persist(int $key, mixed $value): void
    {
        try {
            $response = $this->cacheAdapter->set($key, $value);

            if (!$response) {
                throw new Exception();
            }
        } catch (Throwable $exception) {
            $this->logger->warning($exception->getMessage());

            return;
        }
    }

    public function fetch($key)
    {
        try {
            return $this->cacheAdapter->get($key);
        } catch (InvalidArgumentException $exception) {
            return ['error'];
        }
    }
}
