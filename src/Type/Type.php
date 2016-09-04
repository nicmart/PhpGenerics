<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\Transformer\TypeTransformer;

/**
 * Interface Type
 * @package NicMart\Generics\Type
 */
interface Type
{
    /**
     * @return FullName
     */
    public function name();

    /**
     * @param TypeTransformer $typeTransformer
     * @return Type
     */
    public function map(TypeTransformer $typeTransformer);

    /**
     * @param $z
     * @param callable $fold
     * @return mixed
     */
    public function bottomUpFold($z, callable $fold);

    /**
     * @return mixed
     */
    public function __toString();
}