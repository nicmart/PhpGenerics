<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\Func;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T;
use NicMart\Generics\Example\Func\Predicate«T»;

use NicMart\Generics\Example\Func\CallableFunction1«T·bool»;

class CallablePredicate«T» extends CallableFunction1«T·bool» implements Predicate«T»
{

}