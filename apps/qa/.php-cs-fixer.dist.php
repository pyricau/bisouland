<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

/**
 * PHP CS Fixer documentation:
 * - Homepage: https://cs.symfony.com/
 * - List of all available rules: https://cs.symfony.com/doc/rules/index.html
 * - List of all available rule sets: https://cs.symfony.com/doc/ruleSets/index.html
 * - Find / Compare / See History rules: https://mlocati.github.io/php-cs-fixer-configurator
 *
 * To inspect a specific rule (e.g. `blank_line_before_statement`), run:
 *
 * ```console
 * > php-cs-fixer describe blank_line_before_statement
 * ```
 *
 * ------------------------------------------------------------------------------
 *
 * `new \PhpCsFixer\Finder()` is equivalent to:
 *
 * ```php
 * \Symfony\Component\Finder\Finder::create()
 *     ->files()
 *     ->name('/\.php$/')
 *     ->exclude('vendor')
 *     ->ignoreVCSIgnored(true) // Follow rules establish in .gitignore
 *     ->ignoreDotFiles(false) // Do not ignore files starting with `.`, like `.php-cs-fixer-dist.php`
 * ;
 * ```
 */

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__,
        __DIR__.'/../monolith',
    ])
    ->exclude([
        'var',
    ])
    ->notPath([
        // Note: `notPath()` expect paths relatives to the ones provided in `in()`
        // The rule's fixes from `[PSR-2] statement_indentation` break the following files, so excluding them:
        'phpincludes/bisous.php',
        'phpincludes/cerveau.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        // —— CS Rule Sets —————————————————————————————————————————————————————
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP8x4Migration' => true,
        '@PHP8x2Migration:risky' => true,

        // —— Overriden rules ——————————————————————————————————————————————————

        // [Symfony] `snake_case` (phpspec style) instead of `camelCase`
        'php_unit_method_casing' => ['case' => 'snake_case'],

        // [Symfony] adding `['elements']['parameters']` (Symfony doesn't have it)
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => [
                'arguments',
                'array_destructuring',
                'arrays',
                'match',
                'parameters',
            ],
        ],

        // [Symfony] add use statements instead of allowing FQCNs
        'fully_qualified_strict_types' => ['import_symbols' => true],

        // [PHP8x4Migration] `same_as_start` instead of `start_plus_one`
        'heredoc_indentation' => ['indentation' => 'same_as_start'],

        // —— Disabed rules due to breaking changes ————————————————————————————

        // [PHP7x0Migration:risky] Disabled as the fixes break the following files:
        // 1) ../monolith/phpincludes/nuage.php:173 Warning: Trying to access array offset on false
        // 2) ../monolith/phpincludes/infos.php:221 Warning: Undefined array key 0
        // 3) ../monolith/phpincludes/infos.php:221 Fatal error: Uncaught TypeError: count(): Argument #1 ($value) must be of type Countable|array, null given
        // 4) ../monolith/phpincludes/topten.php:43 Warning: Trying to access array offset on false
        // 5) ../monolith/phpincludes/topten.php:52 Warning: Trying to access array offset on false
        // 6) ../monolith/phpincludes/topten.php:53 Warning: Trying to access array offset on false
        // 7) ../monolith/phpincludes/fctIndex.php:201 Fatal error: Uncaught TypeError: number_format(): Argument #1 ($num) must be of type int|float, string given
        'declare_strict_types' => false,


        // —— Additional rules —————————————————————————————————————————————————

        // [PhpCsFixer]
        'heredoc_to_nowdoc' => true,
    ])
    // While waiting for PHP CS Fixer to support PHP 8.5
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setUsingCache(true)
    ->setFinder($finder)
;
