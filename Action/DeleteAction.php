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

use Pablodip\AdminModuleBundle\BatchAction\DeleteBatchAction;

/**
 * DeleteAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DeleteAction extends RouteAction
{
    /**
     * {@inheritdoc}
     */
    protected function defineConfiguration()
    {
        $this
            ->setRoute('delete', '/{id}', 'DELETE')
            ->setController(array($this, 'controller'))
            ->addOptions(array(
                'redirection_url' => null,
            ))
        ;

        $this->getModule()->getAction('list')->getOption('model_actions')->add(array(
            'delete' => 'PablodipAdminModuleBundle::dataActions/delete.html.twig',
        ));
        $this->getModule()->getAction('batch')->getOption('batch_actions')->add(array(
            'delete' => new DeleteBatchAction('Delete'),
        ));
    }

    public function controller($id)
    {
        $model = $this->getMolino()->findOneById($this->getModuleOption('model_class'), $id);
        if (!$model) {
            throw $this->createNotFoundException();
        }

        $this->getMolino()->delete($model);

        return $this->redirect($this->getRedirectionUrl());
    }

    private function getRedirectionUrl()
    {
        $redirectionUrl = $this->getOption('redirection_url');

        if ($redirectionUrl === null) {
            return $this->generateModuleUrl('list');
        }

        if (is_callable($redirectionUrl)) {
            return call_user_func($redirectionUrl);
        }

        return $redirectionUrl;
    }
}
