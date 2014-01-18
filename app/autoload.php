<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * @var $loader \Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
