<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;

use NicMart\Generics\Name\FullName;

/**
 * Interface ReferenceType
 * @package NicMart\Generics\Type
 */
interface ReferenceType extends Type
{
    /**
     * @return FullName
     */
    public function name();
}