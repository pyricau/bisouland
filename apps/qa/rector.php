<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclarationDocblocks\Rector\Class_\AddReturnDocblockDataProviderRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\ClassMethodArrayDocblockParamFromLocalCallsRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\DocblockVarArrayFromGetterReturnRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\DocblockVarArrayFromPropertyDefaultsRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\DocblockVarFromParamDocblockInConstructorRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddParamArrayDocblockBasedOnArrayMapRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddParamArrayDocblockFromAssignsParamToParamReferenceRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddParamArrayDocblockFromDataProviderRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddParamArrayDocblockFromDimFetchAccessRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForArrayDimAssignedObjectRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForCommonObjectDenominatorRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForJsonArrayRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\DocblockGetterReturnArrayFromPropertyDocblockVarRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\DocblockReturnArrayFromDirectArrayInstanceRector;
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
    ])
    ->withSkip([
        // —— Excluded paths ———————————————————————————————————————————————————
        // Excluded folders
        // [qa]
        __DIR__.'/vendor',
        // [monolith]
        __DIR__.'/../monolith/vendor',

        // —— Excluded rules ———————————————————————————————————————————————————
        // [CODE_QUALITY]
        Rector\CodeQuality\Rector\Assign\CombinedAssignRector::class,
        // [CODING_STYLE]
        Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
    ])
    ->withSets([
        // —— PHP ——————————————————————————————————————————————————————————————
        SetList::PHP_84,

        // —— Core —————————————————————————————————————————————————————————————
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::STRICT_BOOLEANS,
        SetList::TYPE_DECLARATION,

        // —— PHPUnit ——————————————————————————————————————————————————————————
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_120,
    ])
    ->withRules([
        // —— Core —————————————————————————————————————————————————————————————
        // PHPdoc array types
        AddParamArrayDocblockBasedOnArrayMapRector::class,
        AddParamArrayDocblockFromAssignsParamToParamReferenceRector::class,
        AddParamArrayDocblockFromDataProviderRector::class,
        AddParamArrayDocblockFromDimFetchAccessRector::class,
        AddReturnDocblockDataProviderRector::class,
        AddReturnDocblockForArrayDimAssignedObjectRector::class,
        AddReturnDocblockForCommonObjectDenominatorRector::class,
        AddReturnDocblockForJsonArrayRector::class,
        ClassMethodArrayDocblockParamFromLocalCallsRector::class,
        DocblockGetterReturnArrayFromPropertyDocblockVarRector::class,
        DocblockReturnArrayFromDirectArrayInstanceRector::class,
        DocblockVarArrayFromGetterReturnRector::class,
        DocblockVarArrayFromPropertyDefaultsRector::class,
        DocblockVarFromParamDocblockInConstructorRector::class,

        // Inherit parent visibility
        ChangeConstantVisibilityRector::class,
        ChangeMethodVisibilityRector::class,

        // More Coding Style
        ArraySpreadInsteadOfArrayMergeRector::class,
        StaticClosureRector::class,
    ]);
