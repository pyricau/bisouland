<?php

declare(strict_types=1);

namespace Bl\Domain\Auth;

interface SaveAuthToken
{
    public function save(AuthToken $authToken): void;
}
