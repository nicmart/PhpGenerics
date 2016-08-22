<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor\Visitor;


use NicMart\Generics\AST\Visitor\Action\EnterNodeAction;
use NicMart\Generics\AST\Visitor\Action\LeaveNodeAction;
use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\Visitor;
use NicMart\Generics\Infrastructure\PhpDocumentor\Adapter\PhpDocContextAdapter;
use phpDocumentor\Reflection\DocBlockFactory;
use PhpParser\Node;

class PhpDocTypeAnnotatorVisitor implements Visitor
{
    const ATTR_NAME = "PhpDoc";

    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;
    /**
     * @var PhpDocContextAdapter
     */
    private $contextAdapter;

    /**
     * PhpDocTypeAnnotatorVisitor constructor.
     * @param DocBlockFactory $docBlockFactory
     * @param PhpDocContextAdapter $contextAdapter
     */
    public function __construct(
        DocBlockFactory $docBlockFactory,
        PhpDocContextAdapter $contextAdapter
    ) {
        $this->docBlockFactory = $docBlockFactory;
        $this->contextAdapter = $contextAdapter;
    }


    /**
     * @param Node $node
     * @return EnterNodeAction
     */
    public function enterNode(Node $node)
    {
        $comment = $node->getDocComment();
        if (!$comment) {
            return new MaintainNode();
        }
        
        $context = $node->getAttribute(NamespaceContextVisitor::ATTR_NAME);

        $node->setAttribute(
            self::ATTR_NAME,
            $this->docBlockFactory->create(
                $comment->getText(),
                $this->contextAdapter->toPhpDocContext($context)
            )
        );

        return new MaintainNode();
    }

    /**
     * @param Node $node
     * @return LeaveNodeAction
     */
    public function leaveNode(Node $node)
    {
        return new MaintainNode();
    }
}