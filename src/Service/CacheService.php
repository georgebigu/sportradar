<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\CacheException;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Redis;

class CacheService
{
    public function __construct(
        private readonly Redis $redisProvider,
        private readonly CacheInterface $cacheAdapter,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws CacheException
     */
    public function persist(string $key, mixed $value): void
    {
        try {
            $response = $this->cacheAdapter->set($key, $value);
            if (!$response) {
                throw new CacheException('Error! Game was not saved!');
            }
        } catch (InvalidArgumentException $exception) {
            $this->logger->warning($exception->getMessage());
            throw new CacheException('Error! Game was not saved!');
        }
    }

    /**
     * @throws CacheException
     */
    public function fetch($key): mixed
    {
        try {
            return $this->cacheAdapter->get($key);
        } catch (InvalidArgumentException $exception) {
            $this->logger->warning($exception->getMessage());
            throw new CacheException('Error! Game info could not be retrieved from storage!');
        }
    }

    /**
     * @throws CacheException
     */
    public function fetchAll(array $keys): array
    {
        try {
            return $this->cacheAdapter->getMultiple($keys);
        } catch (InvalidArgumentException $exception) {
            $this->logger->warning($exception->getMessage());
            throw new CacheException('Error! Games list info could not be retrieved from storage!');
        }
    }

    public function getAllKeys(): array
    {
        return $this->redisProvider->keys('*');
    }
}
