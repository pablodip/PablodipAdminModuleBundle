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

/**
 * RouteAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class RouteAction extends BaseRouteAction
{
    protected function callOptionCallback($optionName, array $arguments = array())
    {
        $callback = $this->getOption($optionName);

        if ($callback !== null) {
            return call_user_func_array($callback, $arguments);
        }
    }
}
