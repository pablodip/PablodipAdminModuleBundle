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
 * BatchActionInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface BatchActionInterface
{
    /**
     * Returns the label.
     *
     * @return string The label.
     */
    function getLabel();

    /**
     * Sets the module.
     *
     * @param ModuleInterface $module The module.
     */
    function setModule(ModuleInterface $module);

    /**
     * Returns the module.
     *
     * @return ModuleInterface The module.
     *
     * @throws \RuntimeException If there is no module.
     */
    function getModule();

    /**
     * Processes the batch for all elements.
     */
    function processAll();

    /**
     * Processes the batch for some ids.
     *
     * @param array $ids An array of ids.
     */
    function processIds(array $ids);
}
