<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\Assign\CombinedAssignRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Config\RectorConfig;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Visibility\Rector\ClassConst\ChangeConstantVisibilityRector;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;

return RectorConfig::configure()
    ->withCache(
        cacheDirectory: '/tmp/rector',
        cacheClass: FileCacheStorage::class,
    )
    ->withPaths([
        __DIR__,
        __DIR__.'/../monolith',
        __DIR__.'/../../packages',
    ])
    ->withSkip([
        // —— Excluded paths ———————————————————————————————————————————————————
        // Excluded folders
        // [qa]
        __DIR__.'/config/reference.php',
        __DIR__.'/templates/maker',
        __DIR__.'/var',
        __DIR__.'/vendor',
        // [monolith]
        __DIR__.'/../monolith/vendor',
        // [packages]
        __DIR__.'/../../packages/*/vendor',

        // —— Excluded rules ———————————————————————————————————————————————————
        // [CODE_QUALITY]
        CombinedAssignRector::class,
        // [CODING_STYLE]
        EncapsedStringsToSprintfRector::class,
        // [NAMING]
        RenameParamToMatchTypeRector::class,
        RenamePropertyToMatchTypeRector::class,
        RenameVariableToMatchMethodCallReturnTypeRector::class,
    ])
    ->withSets([
        // —— PHP ——————————————————————————————————————————————————————————————
        SetList::PHP_85,

        // —— Core —————————————————————————————————————————————————————————————
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::RECTOR_PRESET,
        SetList::TYPE_DECLARATION,
        SetList::TYPE_DECLARATION_DOCBLOCKS,

        // —— PHPUnit ——————————————————————————————————————————————————————————
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_120,
    ])
    ->withRules([
        // —— Core —————————————————————————————————————————————————————————————
        // Inherit parent visibility
        ChangeConstantVisibilityRector::class,
        ChangeMethodVisibilityRector::class,

        // Strict Booleans
        DisallowedEmptyRuleFixerRector::class,

        // More Coding Style
        ArraySpreadInsteadOfArrayMergeRector::class,
        StaticClosureRector::class,
    ]);
