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
 * EditAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class EditAction extends RouteAction
{
    /**
     * {@inheritdoc}
     */
    protected function defineConfiguration()
    {
        $this
            ->setRoute('edit', '/{id}/edit', 'GET')
            ->addOptions(array(
                'heading'  => 'Edit',
                'fields'   => new FieldBag(),
                'template' => 'PablodipAdminModuleBundle:Admin:edit.html.twig',
            ))
            ->setController(array($this, 'controller'))
        ;

        $this->getModule()->getAction('list')->getOption('model_actions')->add(array(
            'edit' => 'PablodipAdminModuleBundle::dataActions/edit.html.twig',
        ));
    }

    public function controller($id)
    {
        $model = $this->getMolino()->findOneById($this->getModuleOption('model_class'), $id);
        if (!$model) {
            throw $this->createNotFoundException();
        }

        $fields = $this->getModule()->getExtension('model')->filterFields($this->getOption('fields'));
        $form = $this->getModule()->createModelForm($model, $fields);

        return $this->render($this->getOption('template'), array(
            'model' => $model,
            'form'  => $form->createView(),
        ));
    }
}
