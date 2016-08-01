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
 * IDEA: just transform type annotations, that are inside attribute 
 * of nodes
 * 
 * Typeannotation and type serialization handled then by parser/serializer.
 * 
 * Extend/decorate parser and after parsing traverse the nodes with a transformer
 * that annotates the types
 * 
 * Extend/decorate prettyprinter and before pretty-printing transform the nodes
 * using type annotations
 * 
 * Class TypeAnnotationTypeToNodeTransformer
 * @package NicMart\Generics\AST\Transformer
 */
class TypeAnnotationTypeToNodeTransformer implements TypeToNodeTransformer
{
    /**
     * @param TypeTransformer $typeTransformer
     * @return NodeTransformer
     */
    public function nodeTransformer(TypeTransformer $typeTransformer)
    {
        // TODO: Implement nodeTransformer() method.
    }
}