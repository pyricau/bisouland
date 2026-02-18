<?php

declare(strict_types=1);

namespace Bl\Game;

use Bl\Exception\ServerErrorException;

/**
 * @object-type Service
 */
interface SearchUsernames
{
    /**
     * @return list<string>
     *
     * @throws ServerErrorException If an unexpected error occurs
     */
    public function search(string $query): array;
}
