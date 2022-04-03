<?php

$finder = PhpCsFixer\Finder::create()->in(['./src', './dev-packages']);
return (new PhpCsFixer\Config())
    ->setFinder($finder);
