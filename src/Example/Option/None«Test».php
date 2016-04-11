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

/**
 * Class NoneTest
 * @package NicMart\Generics\Example\Option
 */
class None«Test» implements Option«Test»
{
    /**
     * @param Test $else
     * @return Test
     */
    public function getOrElse(Test $else)
    {
        return $else;
    }
}