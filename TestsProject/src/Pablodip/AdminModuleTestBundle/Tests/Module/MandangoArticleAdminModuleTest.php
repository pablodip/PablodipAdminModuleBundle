<?php

namespace Pablodip\AdminModuleTestBundle\Tests\Module;

use Molino\Mandango\Molino;

class MandangoArticleRestModuleTest extends TestCase
{
    protected function setUp()
    {
        if (!class_exists('Mongo')) {
            $this->markTestSkipped('Mongo is not available.');
        }

        parent::setUp();
    }

    protected function getRoutePrefix()
    {
        return '/mandango/articles';
    }

    protected function registerMolino()
    {
        return new Molino($this->getMandango());
    }

    protected function getArticleClass()
    {
        return 'Model\PablodipAdminModuleTestBundle\Article';
    }

    protected function cleanDatabase()
    {
        $this->getMandango()->getRepository($this->getArticleClass())->remove();
    }

    private function getMandango()
    {
        return static::$kernel->getContainer()->get('mandango');
    }
}
