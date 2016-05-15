<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\Option;

use NicMart\Generics\Example\Option\Option«T»;
use NicMart\Generics\Example\Option\Option«S»;

use NicMart\Generics\Example\Option\None«T»;
use NicMart\Generics\Example\Option\None«S»;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\S;
use NicMart\Generics\Variable\T;

class OptionMap«S·T» implements Generic
{
    /**
     * @param Option«S» $option
     * @param $callable
     * @return Option«T»
     */
    public static function map(Option«S» $option, $callable)
    {
        if ($option instanceof None«S») {
            return new None«T»();
        }

        return new Some«T»($callable($option->get()));
    }

    /**
     * @param Option«S» $option
     * @param $callable
     * @return Option«T»
     */
    public static function flatMap(Option«S» $option, $callable)
    {
        if ($option instanceof None«S») {
            return new None«T»();
        }

        return $callable($option->get());
    }
}