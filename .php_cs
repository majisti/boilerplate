<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return Config::create()
    ->setUsingCache(true)
    ->setRules([
        '@Symfony' => true,
        'no_useless_return' => true,
        'psr0' => false,
        'phpdoc_add_missing_param_annotation' => false,
        'phpdoc_align' => false,
    ])
    ->setFinder($finder)
;
