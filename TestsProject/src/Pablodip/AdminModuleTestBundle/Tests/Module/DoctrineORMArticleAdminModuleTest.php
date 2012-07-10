<?php

namespace Pablodip\AdminModuleTestBundle\Tests\Module;

use Molino\Doctrine\ORM\Molino;

class DoctrineORMArticleRestModuleTest extends TestCase
{
    protected function getRoutePrefix()
    {
        return '/doctrine/orm/articles';
    }

    protected function registerMolino()
    {
        return new Molino($this->getEntityManager());
    }

    protected function getArticleClass()
    {
        return 'Pablodip\AdminModuleTestBundle\Entity\Article';
    }

    protected function cleanDatabase()
    {
        $this->getEntityManager()->getRepository($this->getArticleClass())
            ->createQueryBuilder('a')
            ->delete()
            ->getQuery()
            ->execute()
        ;
    }

    private function getEntityManager()
    {
        return static::$kernel->getContainer()->get('doctrine')->getEntityManager();
    }
}
