<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
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
        '@PER-CS2.0:risky' => true,

        // —— Overriden rules ——————————————————————————————————————————————————

        // [Symfony] `snake_case` (phpspec style) instead of `camelCase`
        'php_unit_method_casing' => ['case' => 'snake_case'],

        // [PSR-2] Disabled as the fixes break the following files:
        // 1) ../monolith/web/phpincludes/bisous.php
        // 2) ../monolith/web/phpincludes/cerveau.php
        'statement_indentation' => false,

        // [PER-CS2.0] Partially disabled due to PHP version constraints.
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => [
                // 'arguments', For PHP 7.3+
                // 'array_destructuring', For PHP 7.1+
                'arrays',
                // 'match', For PHP 8.0+
                // 'parameters', For PHP 8.0+
            ],
        ],

        // [Symfony] Disabled as the fixes break the following files:
        // 1) ../monolith/web/phpincludes/cerveau.php
        // 2) ../monolith/web/phpincludes/bisous.php
        'statement_indentation' => false,
    ])
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setUsingCache(true)
    ->setFinder($finder)
;
