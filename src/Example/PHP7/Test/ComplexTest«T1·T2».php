<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP7\Test;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T1;
use NicMart\Generics\Variable\T2;

class ComplexTest«T1·T2» implements Generic
{
    /**
     * @var T1
     */
    public $x;

    const A = 10;

    /**
     * @param T1|T2|ComplexTest«T1·T1» $x Test composite phpdoc types
     * @param T2 $y
     * @return ComplexTest«T1·T1»
     */
    public function f(T1 $x, T2 $y): ComplexTest«T1·T1»
    {
        $a = function (T1 $x, ComplexTest«T2·T1» $z) use ($y) {
            $z::A;
            return new self($x, $y);
        };

        $x instanceof T2;

        return T1::class;
    }
}