<?php

/*
 * This file is part of the PablodipAdminModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\AdminModuleBundle\Module;

use Pablodip\AdminModuleBundle\Field\Guesser\DefaultOptionGuesser;
use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Extension\Model\ModelExtension;
use Pablodip\ModuleBundle\Field\FieldBag;
use Pablodip\AdminModuleBundle\Field\Guesser\IdFieldGuesser;
use Pablodip\AdminModuleBundle\Field\Guesser\ValidatorFieldGuesser;
use Pablodip\AdminModuleBundle\AdminSession;
use Pablodip\AdminModuleBundle\Action;

/**
 * AdminModule.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class AdminModule extends Module
{
    private $adminSession;

    /**
     * {@inheritdoc}
     */
    protected function registerExtensions()
    {
        return array_merge(parent::registerExtensions(), array(
            new ModelExtension(),
            $this->registerMolinoExtension(),
        ));
    }

    /**
     * Returns the molino extension.
     *
     * @return BaseMolinoExtension A molino extension.
     */
    abstract protected function registerMolinoExtension();

    /**
     * {@inheritdoc}
     */
    protected function defineConfiguration()
    {
        $this->getOption('model_field_guessers')->add(array(
            new DefaultOptionGuesser(),
            new IdFieldGuesser(),
            new ValidatorFieldGuesser($this->getContainer()->get('validator')->getMetadataFactory()),
        ));

        $this->addOption('admin_session_parameter', 'as');

        $this->addActions(array(
            new Action\ListAction(),
            new Action\BatchAction(),
            new Action\NewAction(),
            new Action\CreateAction(),
            new Action\EditAction(),
            new Action\UpdateAction(),
            new Action\DeleteAction(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function parseConfiguration()
    {
        if ($this->getContainer()->isScopeActive('request')) {
            $this->adminSession = new AdminSession(
                $this->getContainer()->get('request'),
                $this->getContainer()->get('session'),
                $this->getOption('admin_session_parameter')
            );
            $this->addParameterToPropagate($this->getOption('admin_session_parameter'));
        }
    }

    /**
     * Returns the admin session.
     *
     * @return AdminSession The admin session.
     */
    public function getAdminSession()
    {
        return $this->adminSession;
    }

    /**
     * Default method is POST, override if required
     *
     * @param $model
     * @param FieldBag $fields
     * @param array $options
     * @return mixed
     */
    public function createModelForm($model, FieldBag $fields, array $options = array('method' => 'POST'))
    {
        $formBuilder = $this->getContainer()
            ->get('form.factory')
            ->createBuilder('form', $model, $options)
        ;

        foreach ($fields as $field) {
            $type = $field->hasOption('form_type') ? $field->getOption('form_type') : null;
            $options = $field->hasOption('form_options') ? $field->getOption('form_options') : array();
            $options['label'] = $field->getLabel();
            $formBuilder->add($field->getName(), $type, $options);
        }

        return $formBuilder->getForm();
    }
}
