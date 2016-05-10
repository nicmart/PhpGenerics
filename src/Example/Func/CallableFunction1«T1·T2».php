<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\Func;

use NicMart\Generics\Variable\T1;
use NicMart\Generics\Variable\T2;

use NicMart\Generics\Example\Func\Function1«T1·T2»;


/**
 * Class CallableFunction1«T1·T2»
 * @package NicMart\Generics\Example\Func
 */
class CallableFunction1«T1·T2» implements Function1«T1·T2»
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
     * @return T2
     */
    public function __invoke(T1 $x)
    {
        return $this->returns(
            call_user_func($this->callable, $x)
        );
    }

    /**
     * @param T2 $y
     * @return T2
     */
    public function returns(T2 $y)
    {
        return $y;
    }
}