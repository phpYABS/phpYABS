<?php

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'list_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'ordered_imports' => true,
        'phpdoc_to_comment' => false,
        'visibility_required' => ['elements' => ['property', 'method', 'const']],
        'escape_implicit_backslashes' => ['single_quoted' => true],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/application/includes')
            ->in(__DIR__ . '/application/modules')
            ->in(__DIR__ . '/application/templates')
            ->in(__DIR__ . '/bin')
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
    )
;
