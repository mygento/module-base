<?php
$header = <<<EOF
@author Mygento Team
@copyright 2014-2021 Mygento (https://www.mygento.ru)
@package Mygento_Base
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in('.')
    ->exclude('Test/tmp')
    ->name('*.phtml')
    ->ignoreVCSIgnored(true);
$config = new \Mygento\CS\Config\Module($header);
$config->setFinder($finder);
return $config;
