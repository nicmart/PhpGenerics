<?php
/**
 * Created by PhpStorm.
 * User: nic
 * Date: 05/05/2016
 * Time: 20:10
 */

namespace NicMart\Generics\Example\PHP7\Product;

/**
 * Interface Product
 * @package NicMart\Generics\Example\PHP7\Product
 */
interface Product
{
    /*
     * @return int
     */
    public function arity(): int;

    /**
     * @param $n
     * @return mixed
     */
    public function element($n): mixed;

    /**
     * @return \Iterator
     */
    public function iterator(): \Iterator;
}