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
use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use PhpParser\Node;
use PhpParser\Node\Stmt;

/**
 * Class NamespaceContextVisitor
 *
 * Attach the namespace context to each node
 *
 * Stateful visitor.
 *
 * I would prefer changing the semantic of visitors with an immutable
 * approach, in a fold-like fashion
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

    /**
     * NamespaceContextVisitor constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->currentContext = NamespaceContext::emptyContext();
    }

    /**
     * @return NamespaceContext
     */
    public function context()
    {
        return $this->currentContext;
    }

    /**
     * @param Node $node
     * @return EnterNodeAction|void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Namespace_) {
            var_dump("reset ns");
            $this->currentContext = NamespaceContext::emptyContext();
        }

        $node->setAttribute(
            self::ATTR_NAME,
            $this->currentContext
        );

        if ($node instanceof Stmt\Namespace_) {
            var_dump("new ns");
            $this->currentContext = new NamespaceContext(
                Namespace_::fromParts($node->name->parts)
            );
        }

        return new MaintainNode();
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Stmt\Namespace_) {
            //$this->currentContext = new NamespaceContext(
            //    Namespace_::fromParts($node->name->parts)
            //);
        } elseif ($node instanceof Stmt\UseUse) {
            $this->currentContext = $this->currentContext->withUse(
                Use_::fromStrings($node->name, $node->alias)
            );
            var_dump("adding use " . $node->name->toString());
        }

        return new MaintainNode();
    }
}