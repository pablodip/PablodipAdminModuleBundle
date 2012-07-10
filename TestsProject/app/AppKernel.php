<?php

require_once __DIR__.'/autoload.php';

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Mandango\MandangoBundle\MandangoBundle(),
            new Symfony\Bundle\DoctrineBundle\DoctrineBundle(),
            new Pablodip\ModuleBundle\PablodipModuleBundle(),
            new Pablodip\AdminModuleBundle\PablodipAdminModuleBundle(),
            new Pablodip\AdminModuleTestBundle\PablodipAdminModuleTestBundle(),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }
}
