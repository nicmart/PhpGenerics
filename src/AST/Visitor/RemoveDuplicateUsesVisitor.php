<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 23/05/2016, 17:56
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\AST\Visitor;


use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\AST\Visitor\Action\RemoveNode;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use PhpParser\Node;

/**
 * Class RemoveDuplicateUsesVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class RemoveDuplicateUsesVisitor implements Visitor
{
    /**
     * @param Node $node
     * @return MaintainNode|RemoveNode
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\UseUse) {
            /** @var NamespaceContext $context */
            $context = $node->getAttribute(
                NamespaceContextVisitor::ATTR_NAME
            );
            $name = new FullName($node->name->parts);
            if ($context->uses()->hasUseByName($name)) {
                return new RemoveNode();
            }
        } elseif ($node instanceof Node\Stmt\Use_) {
            if (!$node->uses) {
                return new RemoveNode();
            }
        }

        return new MaintainNode();
    }

    /**
     * @param Node $node
     * @return MaintainNode
     */
    public function enterNode(Node $node)
    {
        return new MaintainNode();
    }
}