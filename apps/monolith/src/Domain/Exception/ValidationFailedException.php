<?php

declare(strict_types=1);

namespace Bl\Domain\Exception;

final class ValidationFailedException extends AppException
{
    public const int CODE = 422;
}
