<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP7\Option;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T;

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
    public function getOrElse(T $else): T
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return get_class($this->value);
    }

    /**
     * @return T
     */
    public function get(): T
    {
        return $this->value;
    }

    public function withoutComment() {

    }
}