<?php

/*
 * This file is part of the PablodipAdminModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\AdminModuleBundle\Action;

use Pablodip\ModuleBundle\Field\FieldBag;

/**
 * NewAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class NewAction extends RouteAction
{
    /**
     * {@inheritdoc}
     */
    protected function defineConfiguration()
    {
        $this
            ->setRoute('new', '/new', 'GET')
            ->addOptions(array(
                'heading'  => 'New',
                'fields'   => new FieldBag(),
                'template' => 'PablodipAdminModuleBundle:Admin:new.html.twig',
            ))
            ->setController(array($this, 'controller'))
        ;

        $this->getModule()->getAction('list')->getOption('list_actions')->add(array(
            'new' => 'PablodipAdminModuleBundle:listActions:new.html.twig',
        ));
    }

    public function controller()
    {
        $model = $this->getMolino()->create($this->getModuleOption('model_class'));
        $fields = $this->getModule()->getExtension('model')->filterFields($this->getOption('fields'));
        $form = $this->getModule()->createModelForm($model, $fields);

        return $this->render($this->getOption('template'), array('form' => $form->createView()));
    }
}
