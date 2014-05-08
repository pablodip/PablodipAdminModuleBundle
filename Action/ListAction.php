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

use Pablodip\ModuleBundle\Field\FieldBag;
use Pablodip\ModuleBundle\OptionBag;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pablodip\AdminModuleBundle\Filter\FilterInterface;

/**
 * ListAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ListAction extends RouteAction
{
    /**
     * {@inheritdoc}
     */
    protected function defineConfiguration()
    {
        $this
            ->setRoute('list', '/', 'GET')
            ->setController(array($this, 'controller'))
            ->addOptions(array(
                'heading'                   => 'List',
                'list_fields'               => new FieldBag(),
                'max_per_page_default'      => 10,
                'maxs_per_page'             => array(5, 10, 15, 20, 25, 30, 40, 50),
                'simple_search_fields'      => new FieldBag(),
                'simple_search_parameter'   => 'q',
                'simple_search_default'     => null,
                'advanced_search_fields'    => new FieldBag(),
                'advanced_search_parameter' => 'advanced_search',
                'list_actions'              => new OptionBag(),
                'model_actions'             => new OptionBag(),
                'sort_parameter'            => 'sort',
                'order_parameter'           => 'order',
                'sort_default'              => null,
                'order_default'             => 'asc',
                'template'                  => 'PablodipAdminModuleBundle:Admin:list.html.twig',
            ))
        ;
    }

    public function controller()
    {
        $this->parseOptions();

        $request = $this->get('request');
        $adminSession = $this->getModule()->getAdminSession();

        // query
        $query = $this->getMolino()->createSelectQuery($this->getModuleOption('model_class'));

        // reset
        if ($request->query->get('reset')) {
            $adminSession->remove(array('simple_search_value', 'advanced_search_data', 'sort', 'order', 'page'));
        }

        // simple search
        list ($simpleSearchEnabled, $simpleSearchValue) = $this->processSimpleSearch($query);

        // advance search
        list ($advancedSearchEnabled, $advancedSearchForm) = $this->processAdvancedSearch($query);

        // sort
        list($sort, $order) = $this->processSort($query);

        // max per page
        $maxPerPage = null;
        if ($request->query->has('max_per_page')) {
            if (in_array($maxPerPageRequest = $request->query->get('max_per_page'), $this->getOption('maxs_per_page'))) {
                $maxPerPage = $maxPerPageRequest;
                $adminSession->set('max_per_page', $maxPerPage);
            }
        }
        if (null === $maxPerPage) {
            $maxPerPage = $adminSession->get('max_per_page', $this->getOption('max_per_page_default'));
        }

        // pagerfanta
        $pagerfanta = new Pagerfanta($query->createPagerfantaAdapter());
        $pagerfanta->setMaxPerPage($maxPerPage);

        // page
        if ($page = $request->query->get('page', $adminSession->get('page'))) {
            try {
                $pagerfanta->setCurrentPage($page);
                $adminSession->set('page', $page);
            } catch (NotValidCurrentPageException $e) {
                $pagerfanta->setCurrentPage(1);
            }
        }

        return $this->render($this->getOption('template'), array(
            'pagerfanta'              => $pagerfanta,
            'simple_search_enabled'   => $simpleSearchEnabled,
            'simple_search_value'     => $simpleSearchValue,
            'advanced_search_enabled' => $advancedSearchEnabled,
            'advanced_search_form'    => $advancedSearchForm ? $advancedSearchForm->createView() : $advancedSearchForm,
            'sort'                    => $sort,
            'order'                   => $order,
        ));
    }

    private function parseOptions()
    {
        $modelExtension = $this->getModule()->getExtension('model');
        $this->setOption('list_fields', $modelExtension->filterFields($this->getOption('list_fields')));
        $this->setOption('simple_search_fields', $modelExtension->filterFields($this->getOption('simple_search_fields')));
        $this->setOption('advanced_search_fields', $modelExtension->filterFields($this->getOption('advanced_search_fields')));
    }

    private function processSimpleSearch($query)
    {
        $request = $this->get('request');
        $adminSession = $this->getModule()->getAdminSession();

        $fields = $this->getOption('simple_search_fields');
        $enabled = (Boolean) count($fields);
        $value = null;
        if ($enabled) {
            $value = $request->query->get(
                $this->getOption('simple_search_parameter'),
                $adminSession->get('simple_search_value', $this->getOption('simple_search_default'))
            );
            if ($value) {
                foreach ($fields as $field) {
                    $query->filterLike($field->getName(), sprintf('*%s*', $value));
                }

                $adminSession->set('simple_search_value', $value);
                $adminSession->remove(array('sort', 'order', 'page'));
            }
        }

        return array($enabled, $value);
    }

    private function processAdvancedSearch($query)
    {
        $request = $this->get('request');
        $adminSession = $this->getModule()->getAdminSession();

        $fields = $this->getOption('advanced_search_fields');
        $enabled = (Boolean) count($fields);
        $form = null;
        if ($enabled) {
            $formBuilder = $this->get('form.factory')
                ->createNamedBuilder($this->getOption('advanced_search_parameter'), 'form')
            ;
            $filters = $this->getAdvancedSearchFilters($fields);
            foreach ($filters as $fieldName => $filter) {
                $fieldData = $fields->get($fieldName);
                $filterFormBuilder = $this->get('form.factory')->createNamedBuilder($fieldName, 'form', null, array('label' => $fieldData->getLabel()));
                $filter->buildForm($filterFormBuilder);
                $formBuilder->add($filterFormBuilder);
            }
            $form = $formBuilder->getForm();

            $data = $request->query
                ->get($this->getOption('advanced_search_parameter'), $adminSession->get('advanced_search_data'))
            ;
            if ($data) {
                $form->bind($data);
                if ($form->isValid()) {
                    foreach ($filters as $fieldName => $filter) {
                        if (isset($data[$fieldName])) {
                            $filter->filter($query, $fieldName, $data[$fieldName]);
                        }
                    }

                    $adminSession->set('advanced_search_data', $data);
                }
            }
        }

        return array($enabled, $form);
    }

    private function getAdvancedSearchFilters(FieldBag $fields)
    {
        $filters = array();
        foreach ($fields as $field) {
            if (!$field->hasOption('advanced_search_filter')) {
                if ($field->hasOption('advanced_search_type')) {
                    $field->setOption('advanced_search_filter',
                        $this->transformAdvancedSearchFilter($field->getOption('advanced_search_type'))
                    );
                } else {
                    throw new \RuntimeException(sprintf('The field "%s" does not have neither "advanced_search_type" nor "advanced_search_filter".', $field->getName()));
                }
            }

            $filter = $field->getOption('advanced_search_filter');
            if (!$filter instanceof FilterInterface) {
                throw new \RuntimeException(sprintf('The filter of the advanced search field "%s" is not an instance of FilterInterface.', $field->getName()));
            }

            $filters[$field->getName()] = $filter;
        }

        return $filters;
    }

    private function transformAdvancedSearchFilter($type)
    {
        if ('string' === $type) {
            return new \Pablodip\AdminModuleBundle\Filter\StringFilter($this->get('translator'));
        }
        if ('boolean' === $type) {
            return new \Pablodip\AdminModuleBundle\Filter\BooleanFilter($this->get('translator'));
        }
        if ('integer' === $type) {
            return new \Pablodip\AdminModuleBundle\Filter\IntegerFilter($this->get('translator'));
        }

        throw new \RuntimeException(sprintf('The advanced filter type "%s" cannot be transformed.', $type));
    }

    private function processSort($query)
    {
        $request = $this->get('request');
        $adminSession = $this->getModule()->getAdminSession();

        $sort = $request->query->get($this->getOption('sort_parameter'), $adminSession->get('sort', $this->getOption('sort_default')));
        $order = $request->query->get($this->getOption('order_parameter'), $adminSession->get('order', $this->getOption('order_default')));
        if ($sort && $order) {
            $listFields = $this->getOption('list_fields');
            if (
                $listFields->has($sort)
                &&
                $listFields->get($sort)->hasOption('sortable')
                &&
                $listFields->get($sort)->getOption('sortable')
                &&
                in_array($order, array('asc', 'desc'))
            ) {
                $query->sort($sort, $order);

                $adminSession->set('sort', $sort);
                $adminSession->set('order', $order);
            }
        }

        return array($sort, $order);
    }
}
