<?php
/**
 * Created by PhpStorm.
 * User: nic
 * Date: 05/05/2016
 * Time: 20:13
 */

namespace NicMart\Generics\Example\Product;

use NicMart\Generics\Generic;
use NicMart\Generics\Variable\T1;

/**
 * Interface Product1«T1»
 * @package NicMart\Generics\Example\Product
 */
interface Product1«T1» extends Product, Generic
{
    /**
     * @return T1
     */
    public function _1();
}