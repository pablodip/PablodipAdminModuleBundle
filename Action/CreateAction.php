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
 * CreateAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class CreateAction extends RouteAction
{
    /**
     * {@inheritdoc}
     */
    protected function defineConfiguration()
    {
        $this
            ->setRoute('create', '/', 'POST')
            ->setController(array($this, 'controller'))
            ->addOptions(array(
                'redirection_url' => null,
                'success_text'    => 'The element has been saved',
                'error_text'      => 'There were problems with your submission',
            ))
        ;
    }

    public function controller()
    {
        $newAction = $this->getModule()->getAction('new');

        $model = $this->getMolino()->create($this->getModuleOption('model_class'));
        $fields = $this->getModule()->getExtension('model')->filterFields($newAction->getOption('fields'));
        $form = $this->getModule()->createModelForm($model, $fields);

        $form->bindRequest($this->get('request'));
        if ($form->isValid()) {
            $this->getMolino()->save($model);

            $this->get('session')->setFlash('success', $this->getOption('success_text'));

            return $this->redirect($this->getRedirectionUrl($model));
        }

        $this->get('session')->setFlash('error', $this->getOption('error_text'));

        return $this->render($newAction->getOption('template'), array('form' => $form->createView()));
    }

    private function getRedirectionUrl($model)
    {
        $redirectionUrl = $this->getOption('redirection_url');

        if ($redirectionUrl === null) {
            return $this->generateModuleUrl('list');
        }

        if (is_callable($redirectionUrl)) {
            return call_user_func($redirectionUrl, $model);
        }

        return $redirectionUrl;
    }
}
