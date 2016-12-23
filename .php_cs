<?php

return \PhpCsFixer\Config::create()
    ->setFinder(
        \Symfony\Component\Finder\Finder::create()
            ->files()
            ->name('*.php')
            ->in(array('src', 'tests'))
    )
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
    ]);