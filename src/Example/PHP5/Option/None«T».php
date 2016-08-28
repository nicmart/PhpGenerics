<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP5\Option;

use NicMart\Generics\Variable\T;

class None«T» implements Option«T»
{
    /**
     * @return None«T»
     */
    public static function instance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @param T $else
     * @return T
     */
    public function getOrElse(T $else)
    {
        return $else;
    }
}