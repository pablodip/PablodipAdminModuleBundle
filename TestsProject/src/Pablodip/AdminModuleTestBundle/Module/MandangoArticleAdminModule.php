<?php

namespace Pablodip\AdminModuleTestBundle\Module;

use Pablodip\AdminModuleBundle\Module\AdminModule;
use Pablodip\ModuleBundle\Extension\Molino\MandangoMolinoExtension;

class MandangoArticleAdminModule extends AdminModule
{
    protected function registerMolinoExtension()
    {
        return new MandangoMolinoExtension();
    }

    protected function configure()
    {
        $this
            ->setRouteNamePrefix('mandango_articles_')
            ->setRoutePatternPrefix('/mandango/articles')
        ;

        $this->setOption('model_class', 'Model\PablodipAdminModuleTestBundle\Article');

        $modelFields = $this->getOption('model_fields');
        $modelFields->add(array(
            'title',
            'content' => array('form_type' => 'textarea'),
            'isActive' => array('label' => 'Is Active?'),
        ));
        $this->getAction('list')->getOption('list_fields')->add(array(
            'title'    => array('sortable' => true),
            'isActive',
        ));
        $this->getAction('list')->getOption('simple_search_fields')->add(array('title'));
        $this->getAction('list')->getOption('advanced_search_fields')->add(array(
            'title',
            'isActive',
        ));
        $this->getAction('new')->getOption('fields')->add($modelFields->keys());
        $this->getAction('edit')->getOption('fields')->add($modelFields->keys());
    }
}
