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
use NicMart\Generics\Name\Assignment\NamespaceAssignment;
use NicMart\Generics\Name\Assignment\NamespaceAssignmentContext;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\RelativeName;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

/**
 * Class TypeNameTransformerVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class TypeNameTransformerVisitor implements Visitor
{
    /**
     * @var NameAssignmentContext
     */
    private $typeAssignmentContext;

    /**
     * @var NamespaceAssignmentContext
     */
    private $namespaceAssignmentContext;

    /**
     * TypeNameTransformerVisitor constructor.
     * @param NameAssignmentContext $typeAssignmentContext
     */
    public function __construct(NameAssignmentContext $typeAssignmentContext)
    {
        $this->typeAssignmentContext = $typeAssignmentContext;
        $this->namespaceAssignmentContext = $this->getNamespaceAssignments();
    }

    /**
     * @param Node $node
     * @return EnterNodeAction
     */
    public function enterNode(Node $node)
    {
        $action = new MaintainNode();

        if ($node instanceof Stmt\Namespace_) {
            $node->name = $this->transformNamespaceName($node->name);
            return $action;
        }

        if ($node instanceof Stmt\Class_ || $node instanceof Stmt\Interface_) {
            $node->name = $this->transformClassName(
                $node->name,
                $this->getNamespaceContext($node)
            );
            return $action;
        }

        return $action;
    }

    /**
     * @param Node $node
     * @return LeaveNodeAction
     */
    public function leaveNode(Node $node)
    {
        return new MaintainNode();
    }

    /**
     * @return NamespaceAssignmentContext
     */
    private function getNamespaceAssignments()
    {
        $assignments = array();

        foreach ($this->typeAssignmentContext->getAssignments() as $typeAssignment) {
            $assignments[] = new NamespaceAssignment(
                new Namespace_($typeAssignment->from()->up()),
                new Namespace_($typeAssignment->to()->up())
            );
        }

        return new NamespaceAssignmentContext($assignments);
    }

    /**
     * @param Node $node
     * @return NamespaceContext
     * @throws \UnexpectedValueException
     */
    private function getNamespaceContext(Node $node)
    {
        if (!$node->hasAttribute(NamespaceContextVisitor::ATTR_NAME)) {
            throw new \UnexpectedValueException(
                "NamespaceContext attribute not found for node"
            );
        }

        return $node->getAttribute(NamespaceContextVisitor::ATTR_NAME);
    }

    /**
     * @param Name $name
     * @return Name
     */
    private function transformNamespaceName(Name $name)
    {
        $from = Namespace_::fromParts($name->parts);
        $to = $this->namespaceAssignmentContext->transformNamespace($from);

        return new Name($to->name()->parts());
    }

    /**
     * @param string $className
     * @param NamespaceContext $namespaceContext
     * @return string
     */
    private function transformClassName($className, NamespaceContext $namespaceContext)
    {
        $fromRelative = new RelativeName((array($className)));
        $from = $namespaceContext->qualifyRelativeName($fromRelative);
        $to = $this->typeAssignmentContext->transformName($from);

        return $to->last()->toString();
    }
}