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
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\Transformer\NameTransformer;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\Node\Name;

/**
 * Class TypeUsageTransformerVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class TypeUsageTransformerVisitor implements Visitor
{
    /**
     * @var NameTransformer
     */
    private $nameTransformer;

    /**
     * TypeUsageTransformerVisitor constructor.
     * @param NameTransformer $nameTransformer
     */
    public function __construct(NameTransformer $nameTransformer)
    {
        $this->nameTransformer = $nameTransformer;
    }

    /**
     * @param Node $node
     * @return EnterNodeAction
     */
    public function enterNode(Node $node)
    {
        // We assume the node has already been decorated by NamespaceContextVisitor
        $nsContext = $node->getAttribute(NamespaceContextVisitor::ATTR_NAME);

        if ($node instanceof Stmt\Class_) {
            if (null !== $node->extends) {
                $node->extends = $this->transformName(
                    $node->extends,
                    $nsContext
                );
            }

            foreach ($node->implements as &$interface) {
                $interface = $this->transformName(
                    $interface,
                    $nsContext
                );
            }
        } elseif ($node instanceof Stmt\Interface_) {
            foreach ($node->extends as &$interface) {
                $interface = $this->transformName($interface, $nsContext);
            }
        } elseif ($node instanceof Expr\StaticCall
           || $node instanceof Expr\StaticPropertyFetch
           || $node instanceof Expr\ClassConstFetch
           || $node instanceof Expr\New_
           || $node instanceof Expr\Instanceof_) {
             if ($node->class instanceof Name) {
                 $node->class = $this->transformName(
                     $node->class,
                     $nsContext
                 );
             }
        } elseif ($node instanceof Stmt\Function_
            || $node instanceof Stmt\ClassMethod
            || $node instanceof Expr\Closure
        ) {
            $this->transformSignature(
                $node,
                $nsContext
            );
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
     * @param Name $name
     * @param NamespaceContext $nsContext
     * @return Name\FullyQualified
     */
    private function transformName(Name $name, NamespaceContext $nsContext)
    {
        $toType = $this->nameTransformer->transform(
            $this->getFullName($name, $nsContext)
        );

        return new Name\FullyQualified($toType->parts(), $name->getAttributes());
    }

    /**
     * @param Node\FunctionLike $function
     * @param NamespaceContext $nsContext
     */
    private function transformSignature(
        Node\FunctionLike $function,
        NamespaceContext $nsContext
    ) {
        foreach ($function->getParams() as $param) {
            if ($param->type instanceof Name) {
                $param->type = $this->transformName(
                    $param->type,
                    $nsContext
                );
            }
        }

        if ($function->getReturnType() instanceof Name) {
            $function->returnType = $this->transformName(
                $function->returnType,
                $nsContext
            );
        }
    }

    /**
     * @param Name $name
     * @param NamespaceContext $nsContext
     * @return FullName
     */
    private function getFullName(Name $name, NamespaceContext $nsContext)
    {
        if ($name->isFullyQualified()) {
            return new FullName($name->parts);
        }

        $relativeName = new RelativeName($name->parts);
        return $nsContext->qualify($relativeName);
    }
}