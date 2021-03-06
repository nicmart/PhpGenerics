<?php
/**
 * Created by PhpStorm.
 * User: nic
 * Date: 05/05/2016
 * Time: 20:18
 */

namespace NicMart\Generics\Example\PHP7\Product;

use NicMart\Generics\Variable\T1;

/**
 * Class Tuple2
 * @package NicMart\Generics\Example\PHP7\Product
 */
class Tuple1«T1» extends AbstractTuple implements Product1«T1»
{
    /**
     * @var T1
     */
    private $x1;

    /**
     * Tuple2«T1·T2» constructor.
     * @param T1 $x1
     */
    public function __construct(T1 $x1)
    {
        $this->x1 = $x1;
    }

    /**
     * @return int
     */
    public function arity(): int
    {
        return 1;
    }

    /**
     * @param $n
     * @return T1
     */
    public function element($n): T1
    {
        $this->assertValidIndex($n);

        return $this->{"x" . ($n + 1)};
    }

    /**
     * @return T1
     */
    public function _1(): T1
    {
        return $this->x1;
    }
}