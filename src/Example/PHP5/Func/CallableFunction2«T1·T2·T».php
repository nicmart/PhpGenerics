<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\PHP5\Func;

use NicMart\Generics\Variable\T1;
use NicMart\Generics\Variable\T2;
use NicMart\Generics\Variable\T;

/**
 * Class CallableFunction2«T1·T2·T»
 * @package NicMart\Generics\Example\PHP5\Func
 */
class CallableFunction2«T1·T2·T» implements Function2«T1·T2·T»
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
     * @param T1 $x
     * @param T2 $y
     * @return T
     */
    public function __invoke(T1 $x, T2 $y)
    {
        return $this->returns(
            call_user_func($this->callable, $x, $y)
        );
    }

    /**
     * @param T $z
     * @return T
     */
    public function returns(T $z)
    {
        return $z;
    }
}