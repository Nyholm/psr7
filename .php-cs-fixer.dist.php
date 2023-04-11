<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests');

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@Symfony' => true,
    '@Symfony:risky' => true,
    'array_syntax' => array('syntax' => 'short'),
    'native_function_invocation' => true,
    'native_constant_invocation' => true,
    'ordered_imports' => true,
    'declare_strict_types' => true,
    'single_import_per_statement' => false,
    'blank_line_after_opening_tag' => false,
    'concat_space' => ['spacing'=>'one'],
    'phpdoc_align' => ['align'=>'left'],
])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
