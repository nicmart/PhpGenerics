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

use NicMart\Generics\AST\Name\PhpParserNameTransformer;
use NicMart\Generics\AST\Visitor\Action\EnterNodeAction;
use NicMart\Generics\AST\Visitor\Action\LeaveNodeAction;
use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\AST\Visitor\Action\RemoveNode;
use NicMart\Generics\AST\Visitor\Action\ReplaceNodeWith;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\Transformer\NameTransformer;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;


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
    public function __construct(
        NameTransformer $nameTransformer
    ) {
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
             if ($node->class instanceof Node\Name ) {
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
        //return new MaintainNode();
        $nsContext = $node->getAttribute(NamespaceContextVisitor::ATTR_NAME);

        if ($node instanceof Stmt\UseUse) {
            $useUse = $this->transformUse($node, $nsContext);

            if (!$useUse) {
                return new RemoveNode();
            }

            return new ReplaceNodeWith($useUse);
        }

        if ($node instanceof Stmt\Use_) {
            if (!$node->uses) {
                return new RemoveNode();
            }
        }

        return new MaintainNode();
    }

    /**
     * @param Node\Name  $phpName
     * @param NamespaceContext $nsContext
     * @return Node\Name\FullyQualified
     */
    private function transformName(Node\Name $phpName, NamespaceContext $nsContext)
    {
        $name = $this->fromPhpNameToName($phpName);
        $transformedName = $this->nameTransformer->transformName(
            $name,
            $nsContext
        );

        // No scalar types in php < 7
        if (!$transformedName->isValidType()) {
            return null;
        }

        return $this->fromNameToPhpName(
            $transformedName
        );
    }

    /**
     * @param Stmt\UseUse $useUse
     * @param NamespaceContext $nsContext
     * @return Node\Name\FullyQualified
     */
    private function transformUse(
        Stmt\UseUse $useUse,
        NamespaceContext $nsContext
    ) {
        $phpName = new Node\Name\FullyQualified($useUse->name->parts);

        $name = $this->fromPhpNameToName($phpName);

        $transformedName = $this->nameTransformer->transformName(
            $name,
            $nsContext
        );

        // No scalar types in php < 7, and don't transform if it is not full
        if (!$transformedName->isValidType()) {
            return null;
        }

        if (!$transformedName instanceof FullName) {
            return $useUse;
        }

        var_dump("FROM: " . $transformedName->toString());
        foreach ($nsContext->uses()->getUsesByAliases() as $use) {
            var_dump("USE " . $use->name()->toString());
        }
        if ($nsContext->uses()->hasUseByName($transformedName)) {
            return null;
        }

        $useUse->name = $this->fromNameToPhpName(
            $transformedName->toRelative()
        );

        if ($phpName->getLast() == $useUse->alias) {
            $useUse->alias = $useUse->name->getLast();
        }

        return $useUse;
    }

    /**
     * @param Node\Name $name
     * @return Name
     */
    private function fromPhpNameToName(Node\Name  $name)
    {
        if ($name->isFullyQualified()) {
            return new FullName($name->parts);
        }

        return new RelativeName($name->parts);
    }

    /**
     * @param Name $name
     * @return Node\Name
     */
    private function fromNameToPhpName(Name $name)
    {
        return $name instanceof FullName && !$name->isNative()
            ? new Node\Name\FullyQualified($name->parts())
            : new Node\Name($name->parts())
        ;
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
            if ($param->type instanceof Node\Name ) {
                $param->type = $this->transformName(
                    $param->type,
                    $nsContext
                );
            }
        }

        if ($function->getReturnType() instanceof Node\Name ) {
            $function->returnType = $this->transformName(
                $function->returnType,
                $nsContext
            );
        }
    }
}