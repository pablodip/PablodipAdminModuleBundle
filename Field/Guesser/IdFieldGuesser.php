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

/**
 * IdFieldGuesser.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class IdFieldGuesser implements FieldGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guessOptions($class, $fieldName)
    {
        $options = array();

        if ('id' === $fieldName) {
            $options[] = new FieldOptionGuess(
                'template',
                'PablodipAdminModuleBundle::fields/text.html.twig',
                FieldOptionGuess::LOW_CONFIDENCE
            );
        }

        return $options;
    }
}
