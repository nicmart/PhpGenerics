<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\Func;

use NicMart\Generics\Variable\T;

use NicMart\Generics\Example\Func\Supplier«T»;

/**
 * Class CallableSupplier«T»
 * @package NicMart\Generics\Example\Func
 */
class CallableSupplier«T» implements Supplier«T»
{
    private $callable;

    /**
     * @param $callable
     */
    public function __construct($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(
                "Callable must be a valid callable"
            );
        }

        $this->callable = $callable;
    }

    /**
     * @return T
     */
    public function __invoke()
    {
        return $this->returns(call_user_func($this->callable));
    }

    /**
     * @param T $x
     * @return T
     */
    private function returns(T $x)
    {
        return $x;
    }
}