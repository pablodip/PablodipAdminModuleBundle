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
 * BooleanFilter.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class BooleanFilter extends BaseFilter
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $formBuilder)
    {
        $formBuilder->add('value', 'choice', array('choices' => array(
            'yes_or_no' => 'Yes or No',
            'yes'       => 'Yes',
            'no'        => 'No',
        )));
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function filter(QueryInterface $query, $fieldName, array $data)
    {
        if ('yes_or_no' == $data['value']) {
            return;
        }
        if ('yes' == $data['value']) {
            $query->filterEqual($fieldName, true);
        } elseif ('no' == $data['value']) {
            $query->filterEqual($fieldName, false);
        }
    }
}
