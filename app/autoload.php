<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';
}
// ADD THIS
$loader->add('Zend', __DIR__.'/../vendor/dodev34/zend-bundle/zend/lib');
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
