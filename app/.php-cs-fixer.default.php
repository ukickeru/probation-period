<?php

$finder = PhpCsFixer\Finder::create()->in(['./src']);
return (new PhpCsFixer\Config())
    ->setFinder($finder);
