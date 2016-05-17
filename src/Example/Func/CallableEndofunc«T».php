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

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T;

use NicMart\Generics\Example\Func\CallableFunction1«T1·T2»;
use NicMart\Generics\Example\Func\CallableFunction1«T·T»;

use NicMart\Generics\Example\Func\Function1«T1·T2»;
use NicMart\Generics\Example\Func\Function1«T·T»;

class CallableEndofunc«T» extends CallableFunction1«T·T» implements Endofunc«T»
{

}