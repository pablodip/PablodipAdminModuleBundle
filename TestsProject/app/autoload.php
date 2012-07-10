<?php

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'Pablodip\\AdminModuleBundle\\')) {
        $path = implode('/', array_slice(explode('\\', $class), 2)).'.php';
        require_once __DIR__.'/../../'.$path;
        return true;
    }
});

$vendorDir = __DIR__.'/../../vendor';
require_once $vendorDir.'/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'                  => $vendorDir.'/symfony/src',
    'Molino'                   => $vendorDir.'/molino/src',
    'Mandango\\Mondator'       => $vendorDir.'/mondator/src',
    'Mandango\\MandangoBundle' => $vendorDir.'/bundles',
    'Mandango'                 => $vendorDir.'/mandango/src',
    'Doctrine\\Common'         => $vendorDir.'/doctrine-common/lib',
    'Doctrine\\MongoDB'        => $vendorDir.'/doctrine-mongodb/lib',
    'Doctrine\\ODM\\MongoDB'   => $vendorDir.'/doctrine-mongodb-odm/lib',
    'Doctrine\\DBAL'           => $vendorDir.'/doctrine-dbal/lib',
    'Doctrine\\ORM'            => $vendorDir.'/doctrine-orm/lib',
    'Doctrine\\Bundle'         => $vendorDir.'/bundles',
    'Metadata'                 => $vendorDir.'/metadata/src',
    'Pablodip'                 => $vendorDir.'/bundles',
    'Pagerfanta'               => $vendorDir.'/pagerfanta/src',
    'WhiteOctober'             => $vendorDir.'/bundles',
    'Assetic'                  => $vendorDir.'/assetic/src',
    'Symfony\\Bundle'          => $vendorDir.'/bundles',
));
$loader->registerPrefixes(array(
    'Twig_' => $vendorDir.'/twig/lib',
));
$loader->registerNamespaceFallbacks(array(
    __DIR__.'/../src',
));
$loader->register();

AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});
AnnotationRegistry::registerFile($vendorDir.'/doctrine-orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
