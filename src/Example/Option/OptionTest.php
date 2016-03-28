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
 * Interface OptionTest
 * @package NicMart\Generics\Example\Option
 */
interface OptionTest
{
    /**
     * @param Test $else
     * @return mixed
     */
    public function getOrElse(Test $else);
}