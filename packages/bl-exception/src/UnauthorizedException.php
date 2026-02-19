<?php

declare(strict_types=1);

namespace Bl\Exception;

final class UnauthorizedException extends AppException
{
    public const int CODE = 401;
}
