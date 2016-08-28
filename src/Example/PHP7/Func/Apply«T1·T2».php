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
use NicMart\Generics\Example\PHP7\Func\Function1«T1·T2»;

class Apply«T1·T2» implements Function2«Function1«T1·T2»·T1·T2», Generic
{
    public function __invoke(Function1«T1·T2» $x, T1 $y): T2
    {
        return $x($y);
    }
}