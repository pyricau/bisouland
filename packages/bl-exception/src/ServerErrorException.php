<?php

declare(strict_types=1);

namespace Bl\Exception;

final class ServerErrorException extends AppException
{
    public const int CODE = 500;
}
