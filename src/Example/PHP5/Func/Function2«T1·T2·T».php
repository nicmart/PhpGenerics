<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP5\Func;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T1;
use NicMart\Generics\Variable\T2;
use NicMart\Generics\Variable\T;

/**
 * Interface Function2«T1·T2·T»
 * @package NicMart\Generics\Example\PHP5\Func
 */
interface Function2«T1·T2·T» extends Generic
{
    /**
     * @param T1 $x
     * @param T2 $y
     * @return T
     */
    public function __invoke(T1 $x, T2 $y);
}