<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Transformer;

use NicMart\Generics\Type\Type;

/**
 * Interface TypeTransformer
 * @package NicMart\Generics\Type\Transformer
 */
interface TypeTransformer
{
    /**
     * @param Type $type
     * @return Type
     */
    public function transform(Type $type);
}