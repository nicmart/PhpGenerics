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


use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Name\Context\NamespaceContext;
use PhpParser\Node;

/**
 * Class SetNamespaceContextNodeTransformer
 * @package NicMart\Generics\AST\Transformer
 */
class SetNamespaceContextNodeTransformer implements NodeTransformer
{
    /**
     * @var NamespaceContext
     */
    private $namespaceContext;
    /**
     * @var PhpNameAdapter
     */
    private $phpNameAdapter;

    /**
     * SetNamespaceContextNodeTransformer constructor.
     * @param NamespaceContext $namespaceContext
     * @param PhpNameAdapter $phpNameAdapter
     */
    public function __construct(
        NamespaceContext $namespaceContext,
        PhpNameAdapter $phpNameAdapter
    ) {
        $this->namespaceContext = $namespaceContext;
        $this->phpNameAdapter = $phpNameAdapter;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        $transformed = [];

        foreach ($nodes as $node) {
            $transformed[] = $node instanceof Node
                ? $this->transformNode($node)
                : $node
            ;
        }

        return $transformed;
    }

    /**
     * @param Node $node
     * @return Node
     */
    private function transformNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Namespace_) {
            return $node;
        }

        // Set Namespace name
        $node->name = $this->phpNameAdapter->toPhpName(
            $this->namespaceContext->namespace_()->name()->toRelative()
        );

        // Remove Use statements
        foreach ($node->stmts as $i => $subNode) {
            if ($subNode instanceof Node\Stmt\Use_) {
                unset($node->stmts[$i]);
            }
        }

        $node->stmts = array_merge(
            $this->newUses(),
            $node->stmts
        );

        return $node;
    }

    /**
     * @return Node\Stmt\Use_[]
     */
    private function newUses()
    {
        $contextUses = $this->namespaceContext->uses()->getUsesByAliases();
        $uses = [];

        foreach ($contextUses as $use) {
            $uses[] = new Node\Stmt\Use_([
                new Node\Stmt\UseUse(
                    $this->phpNameAdapter->toPhpName($use->name()->toRelative()),
                    $use->alias()->toString()
                )
            ]);
        }

        return $uses;
    }
}