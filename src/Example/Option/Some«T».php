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

use NicMart\Generics\Variable\T;

// @todo I don't want to have this one
// Let's add it in the Some«ConcreteClass» generation
use NicMart\Generics\Example\Option\Option«T»;

/**
 * Class Some
 * @package NicMart\Generics\Example
 */
class Some«T» implements Option«T»
{
    /**
     * @var T
     */
    private $value;

    /**
     * Some constructor.
     * @param T $value
     */
    public function __construct(T $value)
    {
        $this->value = $value;
    }

    /**
     * @param T $else
     * @return T
     */
    public function getOrElse(T $else)
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function type()
    {
        return get_class($this->value);
    }

    /**
     * @return T
     */
    public function get()
    {
        return $this->value;
    }

    public function withoutComment() {

    }
}