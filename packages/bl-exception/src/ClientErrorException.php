<?php

declare(strict_types=1);

namespace Bl\Exception;

final class ClientErrorException extends AppException
{
    public const int CODE = 400;
}
