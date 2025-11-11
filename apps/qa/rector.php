<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withCache(
        cacheClass: FileCacheStorage::class,
        cacheDirectory: '/tmp/rector',
    )
    ->withPaths([
        __DIR__,
        __DIR__.'/../monolith/web',
    ])
    ->withSkip([
        __DIR__.'/vendor',
        __DIR__.'/../monolith/web/ban',
        __DIR__.'/../monolith/web/images',
        __DIR__.'/../monolith/web/includes',
        __DIR__.'/../monolith/web/polices',
        __DIR__.'/../monolith/web/smileys',
    ])
    ->withSets([
        // PHP
        SetList::PHP_84,

        // Core
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::STRICT_BOOLEANS,
    ])
    ->withRules([
    ]);
