<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic;

use NicMart\Generics\Name\FullName;

/**
 * Interface GenericNameResolver
 * 
 * Transform an applied generic name to the original generic name.
 * 
 * For example, it transforms a Option«string» to an Option«T»
 * 
 * @package NicMart\Generics\Name\Generic
 */
interface GenericNameResolver
{
    /**
     * @param GenericName $appliedGenericName
     * @return FullName
     */
    public function resolve(GenericName $appliedGenericName);
}