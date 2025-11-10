<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/tests')
    ->in(__DIR__.'/../monolith/web')
    ->exclude('ban')
    ->exclude('images')
    ->exclude('includes')
    ->exclude('polices')
    ->exclude('smileys')
;

return (new PhpCsFixer\Config())
    ->setRules([
        // —— CS Rule Sets —————————————————————————————————————————————————————
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP7x0Migration' => true,
        '@PHP7x0Migration:risky' => true,

        // —— Overriden rules ——————————————————————————————————————————————————

        // [Symfony] `snake_case` (phpspec style) instead of `camelCase`
        'php_unit_method_casing' => ['case' => 'snake_case'],

        // —— Disabed rules due to breaking changes ————————————————————————————

        // [PSR-2] Disabled as the fixes break the following files:
        // 1) ../monolith/web/phpincludes/bisous.php
        // 2) ../monolith/web/phpincludes/cerveau.php
        'statement_indentation' => false,

        // [PHP7x0Migration:risky] Disabled as the fixes break the following files:
        // 1) ../monolith/phpincludes/nuage.php:173 Warning: Trying to access array offset on false
        // 2) ../monolith/phpincludes/infos.php:221 Warning: Undefined array key 0
        // 3) ../monolith/phpincludes/infos.php:221 Fatal error: Uncaught TypeError: count(): Argument #1 ($value) must be of type Countable|array, null given
        // 4) ../monolith/phpincludes/topten.php:43 Warning: Trying to access array offset on false
        // 5) ../monolith/phpincludes/topten.php:52 Warning: Trying to access array offset on false
        // 6) ../monolith/phpincludes/topten.php:53 Warning: Trying to access array offset on false
        // 7) ../monolith/phpincludes/fctIndex.php:201 Fatal error: Uncaught TypeError: number_format(): Argument #1 ($num) must be of type int|float, string given
        'declare_strict_types' => false,
    ])
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setUsingCache(true)
    ->setFinder($finder)
;
