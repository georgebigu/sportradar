<?php

namespace App\Exception;

use Throwable;

class CacheException extends \Exception
{
    private const DEFAULT_MESSAGE = 'Unknown error';
    private const STATUS_CODE = -1;

    public function __construct($message = null, $code = null, Throwable $previous = null)
    {
        parent::__construct(
            $message ?? static::DEFAULT_MESSAGE,
            $code ?? static::STATUS_CODE,
            $previous
        );
    }
}
