<?php

namespace Tests;

use Codeception\Module\Symfony2;

class DataConfiguration
{
    /*
     * It seems like we need to track down which helper we really want to use, and using one single constant
     * will be better than hardcoding the string every time. Whenever we need to use a module, we should add its proper
     * constant until we find a better solution.
     */
    const HELPER_SYMFONY2 = 'Symfony2';

    /**
     * @return string
     */
    public static function dataDir()
    {
        return dirname(__DIR__).'/_data/';
    }
}
