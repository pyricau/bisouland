<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withCache(
        cacheDirectory: '/tmp/rector',
        cacheClass: FileCacheStorage::class,
    )
    ->withPaths([
        __DIR__,
        __DIR__.'/../monolith/web',
    ])
    ->withSkip([
        // —— Excluded paths ———————————————————————————————————————————————————
        // Excluded folders
        // [qa]
        __DIR__.'/vendor',
        // [monolith]
        __DIR__.'/../monolith/web/ban',
        __DIR__.'/../monolith/web/images',
        __DIR__.'/../monolith/web/includes',
        __DIR__.'/../monolith/web/polices',
        __DIR__.'/../monolith/web/smileys',

        // —— Excluded rules ———————————————————————————————————————————————————
        // [CODE_QUALITY]
        \Rector\CodeQuality\Rector\Assign\CombinedAssignRector::class,
        // [CODING_STYLE]
        \Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
    ])
    ->withSets([
        // PHP
        SetList::PHP_84,

        // Core
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::STRICT_BOOLEANS,
    ])
    ->withRules([
    ]);
