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
 * Class Functions«T»
 * @package NicMart\Generics\Example\PHP7\Func
 */
class Functions«T» implements Generic
{
    /**
     * @return UnaryOperator«T»
     */
    public static function identity(): UnaryOperator«T»
    {
        return new CallableUnaryOperator«T»(function ($x) { return $x; });
    }

    /**
     * @param T $x
     * @return Supplier«T»
     */
    public static function constant(T $x): Supplier«T»
    {
        return new CallableSupplier«T»(function () use ($x) { return $x; });
    }
}