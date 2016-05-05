<?php
/**
 * Created by PhpStorm.
 * User: nic
 * Date: 05/05/2016
 * Time: 20:10
 */

namespace NicMart\Generics\Example\Product;

/**
 * Interface Product
 * @package NicMart\Generics\Example\Product
 */
interface Product
{
    /*
     * @return int
     */
    public function arity();

    /**
     * @param $n
     * @return mixed
     */
    public function element($n);

    /**
     * @return \Iterator
     */
    public function iterator();
}