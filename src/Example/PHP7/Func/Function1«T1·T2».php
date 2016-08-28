<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP7\Func;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T1;
use NicMart\Generics\Variable\T2;

/**
 * Interface Function1«T1·T2»
 * @package NicMart\Generics\Example\PHP7\Func
 */
interface Function1«T1·T2» extends Generic
{
    /**
     * @param T1 $x
     * @return T2
     */
    public function __invoke(T1 $x): T2;
}