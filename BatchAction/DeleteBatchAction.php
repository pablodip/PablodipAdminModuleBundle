<?php

/*
 * This file is part of the PablodipAdminModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\AdminModuleBundle\BatchAction;

/**
 * DeleteBatchAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DeleteBatchAction extends BatchAction
{
    /**
     * {@inheritdoc}
     */
    public function processAll()
    {
        $this->createDeleteQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function processIds(array $ids)
    {
        $ids = array_map('intval', $ids);
        $this->createDeleteQuery()->filterIn('id', $ids)->execute();
    }

    private function createDeleteQuery()
    {
        return $this->getMolino()->createDeleteQuery($this->getModule()->getOption('model_class'));
    }
}
