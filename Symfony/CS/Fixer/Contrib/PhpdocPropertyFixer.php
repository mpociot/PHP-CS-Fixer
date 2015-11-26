<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractPhpdocTagsFixer;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
final class PhpdocPropertyFixer extends AbstractPhpdocTagsFixer
{
    /**
     * {@inheritdoc}
     */
    protected static $search = array('property-read', 'property-write');

    /**
     * {@inheritdoc}
     */
    protected static $replace = 'property';

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@property tags should be used rather than other variants.';
    }
}
