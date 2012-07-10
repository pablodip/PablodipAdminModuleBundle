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

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Filter.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseFilter implements FilterInterface
{
    private $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator A translator.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns the translator.
     *
     * @param TranslatorInterface The translator.
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}
