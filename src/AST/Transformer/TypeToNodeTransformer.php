<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer;


use NicMart\Generics\Type\Transformer\TypeTransformer;

/**
 * Interface TypeToNodeTransformer
 * @package NicMart\Generics\AST\Transformer
 *
 * TypeTransformer => NodeTransformer
 */
interface TypeToNodeTransformer
{
    /**
     * @param TypeTransformer $typeTransformer
     * @return NodeTransformer
     */
    public function nodeTransformer(TypeTransformer $typeTransformer);
}