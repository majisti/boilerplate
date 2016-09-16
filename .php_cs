<?php

$finder = Symfony\CS\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->exclude(__DIR__.'/tests/_support/_generated')
;

return Symfony\CS\Config::create()
    ->setUsingCache(true)
    ->fixers(['-empty_return', '-psr0'])
    ->finder($finder)
;
