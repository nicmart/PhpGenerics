<?php
/**
 * Created by PhpStorm.
 * User: nic
 * Date: 05/05/2016
 * Time: 20:16
 */

namespace NicMart\Generics\Example\Product;

use NicMart\Generics\Variable\T1;
use NicMart\Generics\Variable\T2;
use NicMart\Generics\Example\Product\Product;

/**
 * Interface Product2«T1·T2»
 * @package NicMart\Generics\Example\Product
 */
interface Product2«T1·T2» extends Product
{
    /**
     * @return T1
     */
    public function _1();

    /**
     * @return T2
     */
    public function _2();
}