<?php

/*
 * This file is part of the PablodipAdminModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\AdminModuleBundle\Field\Guesser;

use Pablodip\ModuleBundle\Field\Guesser\FieldGuesserInterface;
use Pablodip\ModuleBundle\Field\Guesser\FieldOptionGuess;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Constraint;

/**
 * ValidatorFieldGuesser.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ValidatorFieldGuesser implements FieldGuesserInterface
{
    private $metadataFactory;

    /**
     * Constructor.
     *
     * @param MetadataFactoryInterface $metadataFactory The metadata factory.
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function guessOptions($class, $fieldName)
    {
        $options = array();

        $classMetadata = $this->metadataFactory->getMetadataFor($class);
        // normal and camelized for getters
        foreach (array($fieldName, Container::camelize($fieldName)) as $name) {
            if ($classMetadata->hasMemberMetadatas($name)) {
                foreach ($classMetadata->getMemberMetadatas($name) as $memberMetadata) {
                    foreach ($memberMetadata->getConstraints() as $constraint) {
                        $options = array_merge($options, $this->guessOptionsForConstraint($constraint));
                    }
                }
            }
        }

        return $options;
    }

    private function guessOptionsForConstraint(Constraint $constraint)
    {
        $options = array();

        switch (get_class($constraint)) {
            case 'Symfony\Component\Validator\Constraints\Type':
                switch ($constraint->type) {
                    case 'boolean':
                    case 'bool':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'PablodipAdminModuleBundle::fields/boolean.html.twig',
                            FieldOptionGuess::HIGH_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'advanced_search_type',
                            'boolean',
                            FieldOptionGuess::HIGH_CONFIDENCE
                        );
                        break;
                    case 'double':
                    case 'float':
                    case 'numeric':
                    case 'real':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'PablodipAdminModuleBundle::fields/float.html.twig',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        break;
                    case 'integer':
                    case 'int':
                    case 'long':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'PablodipAdminModuleBundle::fields/integer.html.twig',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        break;
                    case 'string':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'PablodipAdminModuleBundle::fields/text.html.twig',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'advanced_search_type',
                            'string',
                            FieldOptionGuess::HIGH_CONFIDENCE
                        );
                        break;
                    case '\DateTime':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'PablodipAdminModuleBundle::fields/date.html.twig',
                            FieldOptionGuess::MEDIUM_CONFIDENCE
                        );
                        break;
                }
                break;
            case 'Symfony\Component\Validator\Constraints\Choice':
                break;
            case 'Symfony\Component\Validator\Constraints\Country':
                $options[] = new FieldOptionGuess(
                    'form_type',
                    'country',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Date':
                $options[] = new FieldOptionGuess(
                    'template',
                    'PablodipAdminModuleBundle::fields/date.html.twig',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\DateTime':
                $options[] = new FieldOptionGuess(
                    'template',
                    'PablodipAdminModuleBundle::fields/date_time.html.twig',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Email':
                $options[] = new FieldOptionGuess(
                    'template',
                    'PablodipAdminModuleBundle::fields/text.html.twig',
                    FieldOptionGuess::LOW_CONFIDENCE
                );
                $options[] = new FieldOptionGuess(
                    'advanced_search_type',
                    'string',
                    FieldOptionGuess::MEDIUM_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\File':
                break;
            case 'Symfony\Component\Validator\Constraints\Image':
                break;
            case 'Symfony\Component\Validator\Constraints\Ip':
                break;
            case 'Symfony\Component\Validator\Constraints\Language':
                break;
            case 'Symfony\Component\Validator\Constraints\Locale':
                break;
            case 'Symfony\Component\Validator\Constraints\Max':
                break;
            case 'Symfony\Component\Validator\Constraints\MaxLength':
                break;
            case 'Symfony\Component\Validator\Constraints\Min':
                break;
            case 'Symfony\Component\Validator\Constraints\MinLength':
                break;
            case 'Symfony\Component\Validator\Constraints\Regex':
                break;
            case 'Symfony\Component\Validator\Constraints\Time':
                $options[] = new FieldOptionGuess(
                    'template',
                    'PablodipAdminModuleBundle::fields/time.html.twig',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Url':
                break;
        }

        switch (get_class($constraint)) {
            case 'Symfony\Component\Validator\Constraints\NotNull':
                break;
            case 'Symfony\Component\Validator\Constraints\NotBlank':
                break;
            default:
                break;
        }

        return $options;
    }
}
