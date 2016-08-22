<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in('src')
    ->in('tests')
    ->notPath('TestAsset')
    ->notPath('_files')
    ->notPath('share')
    ->filter(function (SplFileInfo $file) {
        if (strstr($file->getPath(), 'compatibility')) {
            return false;
        }
    });
$config = Symfony\CS\Config\Config::create();
$config->level('psr2');
$config->finder($finder);
return $config;
