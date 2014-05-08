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

use Doctrine\Common\Annotations\AnnotationReader;
use Pablodip\ModuleBundle\Field\Guesser\FieldGuesserInterface;
use Pablodip\ModuleBundle\Field\Guesser\FieldOptionGuess;

/**
 * DefaultOptionGuesser
 *
 * @author Rich Sage <rich.sage@gmail.com>
 */
class DefaultOptionGuesser implements FieldGuesserInterface
{
    /**
     * Add a default 'text' display type
     * at low confidence, so anything else can take precedence
     *
     * {@inheritdoc}
     */
    public function guessOptions($class, $fieldName)
    {
        return array(
            new FieldOptionGuess(
                'template',
                'PablodipAdminModuleBundle::fields/text.html.twig',
                FieldOptionGuess::LOW_CONFIDENCE
            ),
        );
    }
}
