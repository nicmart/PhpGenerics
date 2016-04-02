<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor;

use NicMart\Generics\AST\Visitor\Action\EnterNodeAction;
use NicMart\Generics\AST\Visitor\Action\LeaveNodeAction;
use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\Type\Context\Namespace_;
use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\Context\Use_;
use PhpParser\Node;
use PhpParser\Node\Stmt;

/**
 * Class NamespaceContextVisitor
 *
 * Attach the namespace context to each node
 *
 * @package NicMart\Generics\AST\Visitor
 */
class NamespaceContextVisitor implements Visitor
{
    const ATTR_NAME = "namespace_context";

    /**
     * @var NamespaceContext
     */
    private $currentContext;

    public function __construct()
    {
        $this->currentContext = new NamespaceContext(
            new Namespace_("")
        );
    }

    /**
     * @param Node $node
     * @return EnterNodeAction|void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Namespace_) {
            $this->currentContext = NamespaceContext::emptyContext();
        }

        $node->setAttribute(
            self::ATTR_NAME,
            $this->currentContext
        );

        if ($node instanceof Stmt\Namespace_) {
            $this->currentContext = new NamespaceContext(
                Namespace_::fromParts($node->name->parts)
            );
        } elseif ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->currentContext = $this->currentContext->withUse(
                    new Use_($use->name, $use->alias)
                );
            }
        }

        return new MaintainNode();
    }

    public function leaveNode(Node $node)
    {
        // TODO: Implement leaveNode() method.
    }
}