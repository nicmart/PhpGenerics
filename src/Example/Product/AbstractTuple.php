<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Example\Product;


/**
 * Class AbstractTuple
 * @package NicMart\Generics\Example\Product
 */
abstract class AbstractTuple implements Product
{
    /**
     * @param $n
     */
    protected function assertValidIndex($n)
    {
        if ($n >= $this->arity()) {
            throw new \UnexpectedValueException(
                "Index must be less than the arity"
            );
        }
    }

    /**
     * @return \Iterator
     */
    public function iterator()
    {
        return new \ArrayIterator(array_values((array) $this));
    }
}