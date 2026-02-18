<?php

declare(strict_types=1);

namespace Bl\Qa\Domain\Game;

/**
 * @object-type Service
 */
interface SearchUsernames
{
    /**
     * @return list<string>
     */
    public function search(string $query): array;
}
