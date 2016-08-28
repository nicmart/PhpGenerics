<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP5\Option;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T;

/**
 * Interface Option
 * @package NicMart\Generics\Example
 */
interface Option«T» extends Generic
{
    /**
     * @param T|T|Option«T»|Option«Option«T»» $else (Types inserted just to test)
     * @return mixed
     */
    public function getOrElse(T $else);
}