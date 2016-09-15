<?php

return Symfony\CS\Config\Config::create()
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/tests')
    )
;
