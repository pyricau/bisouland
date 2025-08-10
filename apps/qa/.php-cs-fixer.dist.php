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
        '@PER-CS2.0' => true,
        '@PER-CS2.0:risky' => true,

        // [PSR-2] Disabled as the fixes break the following files:
        // 1) ../monolith/web/phpincludes/bisous.php
        // 2) ../monolith/web/phpincludes/cerveau.php
        'statement_indentation' => false,

        // [PER-CS2.0]
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => [
                // 'arguments', disabled as this is only supported from PHP 7.3+
                // 'array_destructuring', disabled as this is only supported from PHP 7.1+
                'arrays',
                // 'match', disabled as this is only supported from PHP 8.0+
                // 'parameters', disabled as this is only supported from PHP 8.0+
            ],
        ],
    ])
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setUsingCache(true)
    ->setFinder($finder)
;
