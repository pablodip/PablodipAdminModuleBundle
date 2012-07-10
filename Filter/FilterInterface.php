<?php

/*
 * This file is part of the PablodipAdminModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\AdminModuleBundle\Filter;

use Symfony\Component\Form\FormBuilder;
use Molino\QueryInterface;

/**
 * FilterInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface FilterInterface
{
    /**
     * Builds the filter form.
     *
     * @param FormBuilder A form builder.
     */
    function buildForm(FormBuilder $formBuilder);

    /**
     * Returns the constraints for the filter.
     *
     * @return array An array of constraints.
     */
    function getConstraints();

    /**
     * Applies the filter.
     *
     * @param QueryInterface $query     The query.
     * @param string         $fieldName The field name.
     * @param array          $data      The data.
     */
    function filter(QueryInterface $query, $fieldName, array $data);
}
