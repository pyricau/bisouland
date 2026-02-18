<?php

declare(strict_types=1);

namespace Bl\Exception;

class AppException extends \DomainException
{
    public const int CODE = 500;

    final public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function make(string $message, ?\Throwable $previous = null): self
    {
        return new static($message, static::CODE, $previous);
    }
}
