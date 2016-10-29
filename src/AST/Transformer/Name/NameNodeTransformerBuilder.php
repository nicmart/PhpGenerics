<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer\Name;


use NicMart\Generics\AST\Transformer\ByCallableNodeTransformer;
use NicMart\Generics\AST\Transformer\NodeFunctor;
use NicMart\Generics\AST\Transformer\NodeTransformer;
use NicMart\Generics\Infrastructure\PhpParser\Name\ChainNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\NameNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\UseUseNameManipulator;
use PhpParser\Node\Name;

/**
 * Class NameNodeTransformerBuilder
 * @package NicMart\Generics\AST\Transformer\Name
 */
class NameNodeTransformerBuilder
{
    /**
     * @param callable $nameTransformer
     * @return NodeTransformer
     */
    public static function build(callable $nameTransformer)
    {
        $nonRecursiveTransformer = new NameManipulatorNodeTransformer(
            new ChainNameManipulator([
                new UseUseNameManipulator(),
                new NameNameManipulator()
            ]),
            $nameTransformer
        );

        return new ByCallableNodeTransformer(
            NodeFunctor::topDown($nonRecursiveTransformer)
        );
    }
}