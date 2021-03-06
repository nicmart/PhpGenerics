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

use NicMart\Generics\Example\PHP7\Func\Function1«T1·T2»;
use NicMart\Generics\Example\PHP7\Func\Function1«T·bool»;

/**
 * Interface Predicate«T»
 * @package NicMart\Generics\Example\PHP7\Func
 */
interface Predicate«T» extends Function1«T·bool», Generic
{

}