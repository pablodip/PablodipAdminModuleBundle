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

use Pablodip\ModuleBundle\Action\BaseRouteAction;
use Pablodip\ModuleBundle\OptionBag;

/**
 * BatchAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class BatchAction extends BaseRouteAction
{
    /**
     * {@inheritdoc}
     */
    protected function defineConfiguration()
    {
        $this
            ->setRoute('batch', '/batch', 'POST')
            ->setController(array($this, 'controller'))
            ->addOptions(array(
                'batch_actions' => new OptionBag(),
            ))
        ;
    }

    public function controller()
    {
        $request = $this->get('request');

        $actions = $this->getOption('batch_actions');
        $actionName = $request->request->get('batch_action');

        // the user has not selected any batch
        if (!$actionName) {
            $this->get('session')->setFlash('warning', 'You have to select an action.');

            return $this->redirect($this->generateModuleUrl('list'));
        }

        // the action does not exist
        if (!$actions->has($actionName)) {
            throw $this->createNotFoundException();
        }

        $action = $actions->get($actionName);
        $action->setModule($this->getModule());

        // all elements
        if ($request->request->get('all')) {
            $action->processAll();
        // selected elements
        } else {
            $ids = $request->request->get('ids');
            // there are no elements selected
            if (!$ids) {
                $this->get('session')->setFlash('warning', 'You have to select some elements.');

                return $this->redirect($this->generateModuleUrl('list'));
            }

            $ids = explode(',', $ids);
            $action->processIds($ids);

            $this->get('session')->setFlash('success', 'The elements were processed successfully.');
        }

        return $this->redirect($this->generateModuleUrl('list'));
    }
}
