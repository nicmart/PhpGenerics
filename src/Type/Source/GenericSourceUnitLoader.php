<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Source;

use NicMart\Generics\Source\SourceUnit;
use NicMart\Generics\Type\GenericType;

/**
 * Interface GenericSourceUnitLoader
 * @package NicMart\Generics\Type\Source
 */
interface GenericSourceUnitLoader
{
    /**
     * @param GenericType $genericType
     * @return SourceUnit
     */
    public function loadSource(GenericType $genericType);
}