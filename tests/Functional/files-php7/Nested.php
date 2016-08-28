<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

use NicMart\Generics\Example\PHP7\Option\Option«T»;
use NicMart\Generics\Example\PHP7\Option\Option«Option«T»»;

// The fact that this compiles means that the generated Option«Option«T»»
// interface is compatible with the definition of the class below

class DoubleOption«T» implements Option«Option«T»»
{
    public function getOrElse(Option«T» $else): Option«T»
    {
        // TODO: Implement getOrElse() method.
    }
}