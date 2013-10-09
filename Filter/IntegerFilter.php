<?php

namespace Pablodip\AdminModuleBundle\Filter;

use Symfony\Component\Form\FormBuilder;
use Molino\QueryInterface;

/**
 * IntegerFilter
 */
class IntegerFilter extends BaseFilter
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $formBuilder)
    {
        $formBuilder->add('type', 'choice', array('choices' => array(
            'equals'                => '=',
            'greater_than'          => '>',
            'greater_than_or_equal' => '>=',
            'less_than'          => '<',
            'less_than_or_equal' => '<=',
        )));
        $formBuilder->add('value', 'integer', array('required' => false));
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
        // can't just test (!$data['value']) as that treats zero as no data
        if ($data['value'] === '') {
            return;
        }

        if ('equals' === $data['type']) {
            $query->filterEqual($fieldName, intval($data['value']));
        } elseif ('greater_than' === $data['type']) {
            $query->filterGreater($fieldName, intval($data['value']));
        } elseif ('greater_than_or_equal' === $data['type']) {
            $query->filterGreaterEqual($fieldName, intval($data['value']));
        } elseif ('less_than' === $data['type']) {
            $query->filterLess($fieldName, intval($data['value']));
        } elseif ('less_than_or_equal' === $data['type']) {
            $query->filterLessEqual($fieldName, intval($data['value']));
        }
    }
}
