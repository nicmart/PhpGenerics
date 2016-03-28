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

/**
 * Class Some
 * @package NicMart\Generics\Example
 */
class Some implements Option
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
     * @return T
     */
    public function get()
    {
        return $this->value;
    }
}