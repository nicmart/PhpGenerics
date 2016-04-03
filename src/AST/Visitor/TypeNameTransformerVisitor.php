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
use NicMart\Generics\Type\Assignment\NamespaceAssignment;
use NicMart\Generics\Type\Assignment\NamespaceAssignmentContext;
use NicMart\Generics\Type\Assignment\TypeAssignmentContext;
use NicMart\Generics\Type\Context\Namespace_;
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
     * @var TypeAssignmentContext
     */
    private $typeAssignmentContext;

    /**
     * @var NamespaceAssignmentContext
     */
    private $namespaceAssignmentContext;

    /**
     * TypeNameTransformerVisitor constructor.
     * @param TypeAssignmentContext $typeAssignmentContext
     */
    public function __construct(TypeAssignmentContext $typeAssignmentContext)
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
        if ($node instanceof Stmt\Namespace_) {
            $node->name = $this->transformNamespaceName($node->name);
        }

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

    /**
     * @return NamespaceAssignmentContext
     */
    private function getNamespaceAssignments()
    {
        $assignments = array();

        foreach ($this->typeAssignmentContext->getAssignments() as $typeAssignment) {
            $assignments[] = new NamespaceAssignment(
                $typeAssignment->from()->namespace_(),
                $typeAssignment->to()->namespace_()
            );
        }

        return new NamespaceAssignmentContext($assignments);
    }

    /**
     * @param Name $name
     * @return Name
     */
    private function transformNamespaceName(Name $name)
    {
        $from = Namespace_::fromParts($name->parts);
        $to = $this->namespaceAssignmentContext->transformNamespace($from);

        return new Name($to->parts());
    }
}