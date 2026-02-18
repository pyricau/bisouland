<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Exception;

final class NotFoundException extends AppException
{
    public const int CODE = 404;
}
