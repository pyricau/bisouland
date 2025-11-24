<?php

declare(strict_types=1);

namespace Bl\Domain\KissBlowing;

/**
 * Blown kiss state enum matching PostgreSQL blown_kiss_state type.
 *
 * Represents the three possible states of a blown kiss mission:
 * - EnRoute: Kiss units are traveling to the target
 * - ComingBack: Mission completed, units returning with loot
 * - CalledOff: Mission was cancelled by the player
 */
enum BlownKissState: string
{
    case EnRoute = 'EnRoute';
    case ComingBack = 'ComingBack';
    case CalledOff = 'CalledOff';
}
