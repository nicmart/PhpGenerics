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
 * Class SomeTest
 * @package NicMart\Generics\Example\Option
 */
class Some«Test» implements Option«Test»
{
    /**
     * @var Test
     */
    private $value;

    /**
     * Some constructor.
     * @param Test $value
     */
    public function __construct(Test $value)
    {
        $this->value = $value;
    }

    /**
     * @param Test $else
     * @return Test
     */
    public function getOrElse(Test $else)
    {
        return $this->value;
    }

    /**
     * @return Test
     */
    public function get()
    {
        return $this->value;
    }
}