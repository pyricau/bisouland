<?php

declare(strict_types=1);

namespace Bl\Auth;

/**
 * @object-type Service
 */
interface SaveAuthToken
{
    public function save(AuthToken $authToken): void;
}
