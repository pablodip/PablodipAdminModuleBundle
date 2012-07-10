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

use Pablodip\ModuleBundle\Module\ModuleInterface;

/**
 * BatchAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BatchAction implements BatchActionInterface
{
    private $label;
    private $module;

    /**
     * Constructor.
     *
     * @param string $label The label.
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setModule(ModuleInterface $module)
    {
        $this->module = $module;
    }

    /**
     * {@inheritdoc}
     */
    public function getModule()
    {
        if (!$this->module) {
            throw new \RuntimeException('There is no module.');
        }

        return $this->module;
    }

    /**
     * Returns the molino.
     *
     * @return MolinoInterface The molino.
     */
    protected function getMolino()
    {
        return $this->getModule()->getExtension('molino')->getMolino();
    }
}
