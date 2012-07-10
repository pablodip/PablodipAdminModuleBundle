<?php

namespace Pablodip\AdminModuleTestBundle\Module;

use Pablodip\AdminModuleBundle\Module\AdminModule;
use Pablodip\ModuleBundle\Extension\Molino\DoctrineORMMolinoExtension;

class DoctrineORMArticleAdminModule extends AdminModule
{
    protected function registerMolinoExtension()
    {
        return new DoctrineORMMolinoExtension();
    }

    protected function configure()
    {
        $this
            ->setRouteNamePrefix('doctrine_orm_articles_')
            ->setRoutePatternPrefix('/doctrine/orm/articles')
        ;

        $this->setOption('model_class', 'Pablodip\AdminModuleTestBundle\Entity\Article');

        $modelFields = $this->getOption('model_fields');
        $modelFields->add(array(
            'title',
            'content',
        ));
        $this->getAction('list')->getOption('simple_search_fields')->add(array('title'));
        $this->getAction('list')->getOption('advanced_search_fields')->add(array(
            'title' => array('advanced_search_type' => 'string')
        ));
        $this->getAction('new')->getOption('fields')->add($modelFields->keys());
        $this->getAction('edit')->getOption('fields')->add($modelFields->keys());
    }
}
