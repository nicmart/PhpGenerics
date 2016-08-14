<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor;


use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\AST\Visitor\Action\ReplaceNodeWith;
use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Name\Context\NamespaceContext;
use PhpParser\Node;

class NameSimplifierVisitor implements Visitor
{
    const ATTR_SKIP = "namesimplifier_skip";

    /**
     * @var NamespaceContextVisitor
     */
    private $namespaceContextVisitor;
    /**
     * @var PhpNameAdapter
     */
    private $phpNameAdapter;

    /**
     * NameTransformerVisitor constructor.
     * @param PhpNameAdapter $phpNameAdapter
     * @param NamespaceContextVisitor $namespaceContextVisitor
     */
    public function __construct(
        PhpNameAdapter $phpNameAdapter,
        NamespaceContextVisitor $namespaceContextVisitor
    ) {
        $this->namespaceContextVisitor = $namespaceContextVisitor;
        $this->phpNameAdapter = $phpNameAdapter;
    }

    public function enterNode(Node $node)
    {
        $this->namespaceContextVisitor->enterNode($node);

        if ($node instanceof Node\Stmt\UseUse) {
            $node->name->setAttribute(
                self::ATTR_SKIP,
                true
            );
        }

        return new MaintainNode();
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Name || $node->hasAttribute(self::ATTR_SKIP)) {
            return new MaintainNode();
        }

        /** @var NamespaceContext $context */
        $this->namespaceContextVisitor->enterNode($node);
        $context = $node->getAttribute(NamespaceContextVisitor::ATTR_NAME);

        return new ReplaceNodeWith(
            $this->phpNameAdapter->toPhpName(
                $context->simplify($context->qualify(
                    $this->phpNameAdapter->fromPhpName($node)
                ))
            )
        );
    }

}