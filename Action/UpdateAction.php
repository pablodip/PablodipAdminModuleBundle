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
 * UpdateAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class UpdateAction extends RouteAction
{
    /**
     * {@inheritdoc}
     */
    protected function defineConfiguration()
    {
        $this
            ->setRoute('update', '/{id}', 'PUT')
            ->setController(array($this, 'controller'))
            ->addOptions(array(
                'pre_save_callback'  => null,
                'post_save_callback' => null,
                'redirection_url'    => null,
                'success_text'       => 'The element has been saved',
                'error_text'         => 'There were problems with your submission',
            ))
        ;
    }

    public function controller($id)
    {
        $model = $this->getMolino()->findOneById($this->getModuleOption('model_class'), $id);
        if (!$model) {
            throw $this->createNotFoundException();
        }

        $editAction = $this->getModule()->getAction('edit');

        $fields = $this->getModule()->getExtension('model')->filterFields($editAction->getOption('fields'));
        $form = $this->getModule()->createModelForm($model, $fields);

        $form->bindRequest($this->get('request'));
        if ($form->isValid()) {
            if ($response = $this->callOptionCallback('pre_save_callback')) {
                return $response;
            }

            $this->getMolino()->save($model);

            if ($response = $this->callOptionCallback('post_save_callback')) {
                return $response;
            }

            $this->get('session')->setFlash('success', $this->getOption('success_text'));

            return $this->redirect($this->getRedirectionUrl($model));
        }

        $this->get('session')->setFlash('error', $this->getOption('error_text'));

        return $this->render($editAction->getOption('template'), array(
            'model' => $model,
            'form'  => $form->createView(),
        ));
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
