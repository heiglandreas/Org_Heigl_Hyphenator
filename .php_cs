<?php
$finder = PhpCsFixer\Finder::create()
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

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
    ])
    ->setFinder($finder);
