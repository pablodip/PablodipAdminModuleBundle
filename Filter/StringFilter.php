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
 * StringFilter.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class StringFilter extends BaseFilter
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $formBuilder)
    {
        $formBuilder->add('type', 'choice', array('choices' => array(
            'contains'     => 'Contains',
            'not_contains' => 'Not contains',
            'exactly'      => 'Exactly',
        )));
        $formBuilder->add('value', 'text', array('required' => false));
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
        // no filter
        if (!$data['value']) {
            return;
        }

        if ('contains' === $data['type']) {
            $query->filterLike($fieldName, sprintf('*%s*', $data['value']));
        } elseif ('not_contains' === $data['type']) {
            $query->filterNotLike($fieldName, sprintf('*%s*', $data['value']));
        } elseif ('exactly' === $data['type']) {
            $query->filterEqual($fieldName, $data['value']);
        }
    }
}
