<?php

// This is global bootstrap for autoloading

use AspectMock\Kernel;

require_once __DIR__.'/../var/bootstrap.php.cache';
require_once __DIR__.'/../vendor/hamcrest/hamcrest-php/hamcrest/Hamcrest.php';

$kernel = Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'appDir' => __DIR__.'/../app',
    'cacheDir' => __DIR__.'/../var/cache',
    'includePaths' => [__DIR__.'/../src'],
    'excludePaths' => [__DIR__], // tests dir should be excluded
]);
