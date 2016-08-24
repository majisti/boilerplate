<?php

use Symfony\Component\Yaml\Yaml;

$params = Yaml::parse(file_get_contents(__DIR__.'/config/parameters.yml'));

return $params['parameters']['symfony_env'] ?? getenv('SYMFONY_ENV') ?: 'prod';
