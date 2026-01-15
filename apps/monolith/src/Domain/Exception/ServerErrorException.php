<?php

declare(strict_types=1);

namespace Bl\Domain\Exception;

final class ServerErrorException extends AppException
{
    public const int CODE = 500;
}
