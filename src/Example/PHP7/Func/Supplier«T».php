<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP7\Func;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T;

/**
 * Interface Supplier«T»
 * @package NicMart\Generics\Example\PHP7\Func
 */
interface Supplier«T» extends Generic
{
    /**
     * @return T
     */
    public function __invoke(): T;
}