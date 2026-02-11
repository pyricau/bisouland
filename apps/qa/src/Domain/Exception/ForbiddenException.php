<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Exception;

final class ForbiddenException extends AppException
{
    public const int CODE = 403;
}
