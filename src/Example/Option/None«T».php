<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\Option;

use NicMart\Generics\Example\Option\Option;
use NicMart\Generics\Variable\T;

class None«T» implements Option«T»
{
    public function getOrElse(T $else)
    {
        return $else;
    }
}