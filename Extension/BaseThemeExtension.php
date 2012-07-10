<?php

/*
 * This file is part of the PablodipAdminModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\AdminModuleBundle\Extension;

use Pablodip\ModuleBundle\Extension\BaseExtension;

/**
 * BaseThemeExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseThemeExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'theme';
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $module = $this->getModule();

        $listAction = $module->getAction('list');
        $listAction->setOption('template', $this->getListActionTemplate());
        $module->getAction('list')->getOption('list_actions')->set('new', $this->getNewListActionTemplate());
        $module->getAction('list')->getOption('model_actions')->set('edit', $this->getEditModelActionTemplate());
        $module->getAction('list')->getOption('model_actions')->set('delete', $this->getDeleteModelActionTemplate());

        $module->getAction('new')->setOption('template', $this->getNewActionTemplate());
        $module->getAction('edit')->setOption('template', $this->getEditActionTemplate());
    }

    abstract protected function getListActionTemplate();

    abstract protected function getNewActionTemplate();

    abstract protected function getEditActionTemplate();

    abstract protected function getNewListActionTemplate();

    abstract protected function getEditModelActionTemplate();

    abstract protected function getDeleteModelActionTemplate();
}
