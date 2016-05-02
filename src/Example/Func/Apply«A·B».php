<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\Func;

use NicMart\Generics\Variable\T1;
use NicMart\Generics\Variable\T2;
use NicMart\Generics\Variable\T;
use NicMart\Generics\Variable\A;
use NicMart\Generics\Variable\B;
use NicMart\Generics\Example\Func\Function1«T1·T2»;
use NicMart\Generics\Example\Func\Function1«A·B»;
use NicMart\Generics\Example\Func\Function2«T1·T2·T»;
use NicMart\Generics\Example\Func\Function2«Function1«T1·T2»·T1·T2»;

class Apply«A·B» extends Function2«Function1«A·B»·A·B»
{
    public function __construct()
    {

    }

    public function __invoke(Function1«A·B» $x, A $y)
    {
        return $x($y);
    }

}