<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP5\Option;

use NicMart\Generics\Example\PHP5\Option\Option«T»;
use NicMart\Generics\Example\PHP5\Option\Option«S»;

use NicMart\Generics\Generic;

use NicMart\Generics\Example\PHP5\Option\None«T»;
use NicMart\Generics\Example\PHP5\Option\None«S»;
use NicMart\Generics\Example\PHP5\Option\Some«S»;

use NicMart\Generics\Variable\S;
use NicMart\Generics\Variable\T;

use NicMart\Generics\Example\PHP5\Func\Function1«S·T»;
use NicMart\Generics\Example\PHP5\Func\Function1«S·Option«T»»;

class OptionMap«S·T» implements Generic
{
    /**
     * @param Option«S» $option
     * @param Function1«S·T» $f
     * @return \NicMart\Generics\Example\PHP5\Option\Option«T»
     */
    public static function map(Option«S» $option, Function1«S·T» $f)
    {
        if ($option instanceof Some«S») {
            return new Some«T»($f($option->get()));
        }

        return None«T»::instance();
    }

    /**
     * @param Option«S» $option
     * @param Function1«S·Option«T»» $f
     * @return Option«T»
     */
    public static function flatMap(
        Option«S» $option,
        Function1«S·Option«T»» $f
    ) {
        if ($option instanceof Some«S») {
            return $f($option->get());
        }

        return None«T»::instance();
    }
}