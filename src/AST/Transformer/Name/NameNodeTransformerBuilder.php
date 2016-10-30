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


use NicMart\Generics\AST\Transformer\BottomUpNodeTransformer;
use NicMart\Generics\AST\Transformer\ByCallableNodeTransformer;
use NicMart\Generics\AST\Transformer\NodeFunctor;
use NicMart\Generics\AST\Transformer\NodeTransformer;
use NicMart\Generics\AST\Transformer\Subnode\ConditionalSubnodeTransformer;
use NicMart\Generics\AST\Transformer\Subnode\ExcludeSubnodesTransformer;
use NicMart\Generics\AST\Transformer\Subnode\SubnodeTransformerCondition;
use NicMart\Generics\Infrastructure\PhpParser\Name\ChainNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\ClassNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\NameNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\UseUseNameManipulator;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\UseUse;

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
                new ClassNameManipulator(),
                new NameNameManipulator()
            ]),
            $nameTransformer
        );

        return new BottomUpNodeTransformer(
            new ConditionalSubnodeTransformer([
                new SubnodeTransformerCondition(
                    new ExcludeSubnodesTransformer(["name"]),
                    Namespace_::class
                ),
                new SubnodeTransformerCondition(
                    new ExcludeSubnodesTransformer(["name"]),
                    UseUse::class
                ),
            ]),
            $nonRecursiveTransformer
        );
    }
}