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

use NicMart\Generics\Variable\T;

use NicMart\Generics\Example\Func\CallableEndofunc«T»;
use NicMart\Generics\Example\Func\CallableSupplier«T»;

/**
 * Class Functions«T»
 * @package NicMart\Generics\Example\Func
 */
class Functions«T»
{
    /**
     * @return CallableEndofunc«T»
     */
    public static function identity()
    {
        return new CallableEndofunc«T»(function ($x) { return $x; });
    }

    /**
     * @param T $x
     * @return CallableSupplier«T»
     */
    public static function constant(T $x)
    {
        return new CallableSupplier«T»(function () use ($x) { return $x; });
    }
}