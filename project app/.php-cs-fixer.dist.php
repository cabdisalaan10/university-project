<?php

// Enforce one consistent, readable style across every PHP source file.
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->exclude('runtime');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'single_space'],
        'concat_space' => ['spacing' => 'one'],
        'no_multiple_statements_per_line' => true,
        'ordered_imports' => true,
        'single_line_comment_style' => true,
        'whitespace_after_comma_in_array' => true,
    ])
    ->setFinder($finder);
