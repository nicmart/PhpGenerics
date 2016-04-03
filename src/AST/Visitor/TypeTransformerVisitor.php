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
use NicMart\Generics\Type\Assignment\TypeAssignmentContext;
use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\RelativeType;
use NicMart\Generics\Type\Type;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

/**
 * Class TypeTransformerVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class TypeTransformerVisitor implements Visitor
{
    /**
     * @var TypeAssignmentContext
     */
    private $typeAssignmentContext;

    /**
     * TypeTransformerVisitor constructor.
     * @param TypeAssignmentContext $typeAssignmentContext
     */
    public function __construct(TypeAssignmentContext $typeAssignmentContext)
    {
        $this->typeAssignmentContext = $typeAssignmentContext;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Expr\StaticCall
           || $node instanceof Expr\StaticPropertyFetch
           || $node instanceof Expr\ClassConstFetch
           || $node instanceof Expr\New_
           || $node instanceof Expr\Instanceof_) {
             if ($node->class instanceof Name) {
                 $node->class = $this->resolveClassName(
                     $node->class,
                     $node->getAttribute(NamespaceContextVisitor::ATTR_NAME)
                 );
             }
         }
    }

    public function leaveNode(Node $node)
    {
        // TODO: Implement leaveNode() method.
    }


    private function resolveClassName(Name $name, NamespaceContext $nsContext)
    {
        // don't resolve special class names
        if (in_array(strtolower($name->toString()), array('self', 'parent', 'static'))) {
            return $name;
        }

        $fromType = RelativeType::fromParts($name->parts)->toFullType($nsContext);
        $toType = $this->typeAssignmentContext->transformType($fromType);

        return new Name\FullyQualified($toType->parts(), $name->getAttributes());
    }

}