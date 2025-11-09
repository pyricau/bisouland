<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/tests',
        __DIR__ . '/../monolith/web',
    ])
    ->withSkip([
        __DIR__ . '/../monolith/web/ban',
        __DIR__ . '/../monolith/web/images',
        __DIR__ . '/../monolith/web/includes',
        __DIR__ . '/../monolith/web/polices',
        __DIR__ . '/../monolith/web/smileys',
    ])
    ->withSets([
        SetList::PHP_73,
    ]);
