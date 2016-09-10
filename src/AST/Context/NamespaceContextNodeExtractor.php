<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Context;


use NicMart\Generics\AST\Transformer\NodeFunctor;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use PhpParser\Node;

/**
 * Class NamespaceContextNodeExtractor
 * @package NicMart\Generics\AST\Context
 */
class NamespaceContextNodeExtractor
{
    /**
     * @param Node $node
     * @return mixed
     */
    public function extract(Node $node)
    {
        return call_user_func(
            NodeFunctor::topDownGenericFold(
                $this->childrenFold(),
                $this->namespaceContextFold()
            ),
            $node,
            NamespaceContext::emptyContext()
        );
    }

    /**
     * @param array $nodes
     * @return NamespaceContext
     */
    public function extractFromArray(array $nodes)
    {
        $ctx = NamespaceContext::emptyContext();
        $fold = NodeFunctor::topDownGenericFold(
            $this->childrenFold(),
            $this->namespaceContextFold()
        );

        foreach ($nodes as $node) {
            $ctx = $fold($node, $ctx);
        }

        return $ctx;
    }

    /**
     * @return \Closure
     */
    private function namespaceContextFold()
    {
        return function (Node $node, NamespaceContext $namespaceContext) {
            return $this->contextOf($node)->merge($namespaceContext);
        };
    }


    /**
     * Extract the context of a node (non-recursively)
     *
     * @param Node $node
     * @return NamespaceContext
     */
    private function contextOf(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            return NamespaceContext::fromNamespaceName(
                $node->name->toString()
            );
        }

        if ($node instanceof Node\Stmt\UseUse) {
            return NamespaceContext::emptyContext()
                ->withUse(Use_::fromStrings(
                    $node->name->toString(),
                    $node->alias
                ))
            ;
        }

        return NamespaceContext::emptyContext();
    }

    /**
     * Recurse only on namespaces and uses
     * @return \Closure
     */
    private function childrenFold() {
        return function (callable $fold) {
            return function (
                Node $node,
                NamespaceContext $namespaceContext
            ) use ($fold) {
                if (!$node instanceof Node\Stmt\Namespace_
                    && !$node instanceof Node\Stmt\Use_
                ) {
                    return $namespaceContext;
                }

                return call_user_func(
                    NodeFunctor::foldChildren($fold),
                    $node,
                    $namespaceContext
                );
            };
        };
    }
}