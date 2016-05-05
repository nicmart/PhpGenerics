<?php
/**
 * Created by PhpStorm.
 * User: nic
 * Date: 05/05/2016
 * Time: 20:18
 */

namespace NicMart\Generics\Example\Product;

use NicMart\Generics\Variable\T1;
use NicMart\Generics\Variable\T2;

/**
 * Class Tuple2
 * @package NicMart\Generics\Example\Product
 */
class Tuple2«T1·T2» extends AbstractTuple implements Product2«T1·T2»
{
    /**
     * @var T1
     */
    private $x1;

    /**
     * @var T2
     */
    private $x2;

    /**
     * Tuple2«T1·T2» constructor.
     * @param T1 $x1
     * @param T2 $x2
     */
    public function __construct(T1 $x1, T2 $x2)
    {
        $this->x1 = $x1;
        $this->x2 = $x2;
    }

    /**
     * @return int
     */
    public function arity()
    {
        return 2;
    }

    /**
     * @param $n
     * @return mixed
     */
    public function element($n)
    {
        $this->assertValidIndex($n);

        return $this->{"x" . ($n + 1)};
    }

    /**
     * @return T1
     */
    public function _1()
    {
        return $this->x1;
    }

    /**
     * @return T2
     */
    public function _2()
    {
        return $this->x2;
    }
}