<?php

set_time_limit(0);

if (!is_dir($vendorDir = __DIR__.'/vendor')) {
    mkdir($vendorDir, 0777, true);
}

$deps = array(
    array('symfony', 'http://github.com/symfony/symfony', isset($_SERVER['SYMFONY_VERSION']) ? $_SERVER['SYMFONY_VERSION'] : 'origin/2.0'),
    array('twig', 'http://github.com/fabpot/Twig', 'origin/master'),
    array('pagerfanta', 'http://github.com/whiteoctober/Pagerfanta', 'origin/master'),
    array('assetic', 'http://github.com/kriswallsmith/assetic', 'origin/master'),
    array('molino', 'http://github.com/pablodip/molino', 'origin/master'),
    array('mondator', 'http://github.com/mandango/mondator', 'origin/master'),
    array('mandango', 'http://github.com/mandango/mandango', 'origin/master'),
    array('doctrine-common', 'http://github.com/doctrine/common', 'origin/master'),
    array('doctrine-dbal', 'http://github.com/doctrine/dbal', 'origin/master'),
    array('doctrine-orm', 'http://github.com/doctrine/doctrine2', 'origin/master'),
    array('doctrine-mongodb', 'http://github.com/doctrine/mongodb', 'origin/master'),
    array('doctrine-mongodb-odm', 'http://github.com/doctrine/mongodb-odm', 'origin/master'),
    array('metadata', 'http://github.com/schmittjoh/metadata', 'origin/master'),
    array('pagerfanta-bundle', 'http://github.com/whiteoctober/WhiteOctoberPagerfantaBundle', 'origin/symfony2.0', 'bundles/WhiteOctober/PagerfantaBundle'),
    array('assetic-bundle', 'http://github.com/symfony/AsseticBundle', 'origin/master', 'bundles/Symfony/Bundle/AsseticBundle'),
    array('mandango-bundle', 'http://github.com/mandango/MandangoBundle', 'origin/master', 'bundles/Mandango/MandangoBundle'),
    //array('doctrine-bundle', 'http://github.com/doctrine/DoctrineBundle', 'origin/master', 'bundles/Doctrine/Bundle/DoctrineBundle'),
    array('pablodip-module-bundle', 'http://github.com/pablodip/PablodipModuleBundle', 'origin/master', 'bundles/Pablodip/ModuleBundle'),
);

foreach ($deps as $dep) {
    if (3 === count($dep)) {
        list($name, $url, $rev) = $dep;
        $target = null;
    } else {
        list($name, $url, $rev, $target) = $dep;
    }

    if (null !== $target) {
        $installDir = $vendorDir.'/'.$target;
    } else {
        $installDir = $vendorDir.'/'.$name;
    }

    $install = false;
    if (!is_dir($installDir)) {
        $install = true;
        echo "> Installing $name\n";

        system(sprintf('git clone %s %s', escapeshellarg($url), escapeshellarg($installDir)));
    }

    if (!$install) {
        echo "> Updating $name\n";
    }

    system(sprintf('cd %s && git fetch origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
}
