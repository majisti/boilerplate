<?php

$finder = Symfony\CS\Finder::create()
    ->in(__DIR__.'/src')
    ->exclude('_support/_generated')
    ->in(__DIR__.'/tests')
;

return Symfony\CS\Config::create()
    ->setUsingCache(true)
    ->fixers(['-empty_return', '-psr0'])
    ->finder($finder)
;
