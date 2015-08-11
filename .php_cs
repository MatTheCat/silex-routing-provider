<?php

$finder = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(array('src', 'tests'));

$config = \Symfony\CS\Config\Config::create()
    ->level(\Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->finder($finder);

return $config;
