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
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;

/**
 * Class TypeParserVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class TypeNameVisitor implements Visitor
{
    /**
     * @var callable
     */
    private $nameVisitor;

    /**
     * TypeParserVisitor constructor.
     * @param callable $nameVisitor
     */
    public function __construct($nameVisitor)
    {
        $this->nameVisitor = $nameVisitor;
    }

    /**
     * @param Node $node
     * @return MaintainNode
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Class_) {
            if (null !== $node->extends) {
                $this->visitName($node->extends);
            }

            foreach ($node->implements as $interface) {
                $this->visitName($interface);
            }
        } elseif ($node instanceof Stmt\Interface_) {
            foreach ($node->extends as $interface) {
                $this->visitName($interface);
            }
        } elseif ($node instanceof Expr\StaticCall
            || $node instanceof Expr\StaticPropertyFetch
            || $node instanceof Expr\ClassConstFetch
            || $node instanceof Expr\New_
            || $node instanceof Expr\Instanceof_) {
            if ($node->class instanceof Node\Name ) {
                $this->visitName($node->class);
            }
        } elseif ($node instanceof Stmt\Function_
            || $node instanceof Stmt\ClassMethod
            || $node instanceof Expr\Closure
        ) {
            $this->visitSignature(
                $node
            );
        } elseif ($node instanceof Stmt\UseUse) {
            $this->visitName($node->name);
        }

        return new MaintainNode();
    }

    /**
     * @param Node\FunctionLike $function
     */
    private function visitSignature(
        Node\FunctionLike $function
    ) {
        foreach ($function->getParams() as $param) {
            if ($param->type instanceof Node\Name ) {
                $this->visitName($param->type);
            }
        }

        if ($function->getReturnType() instanceof Node\Name ) {
            $this->visitName(
                $function->returnType
            );
        }
    }

    /**
     * @param Node $node
     * @return MaintainNode
     */
    public function leaveNode(Node $node)
    {
        return new MaintainNode();
    }

    /**
     * @param Node\Name $name
     * @return mixed
     */
    private function visitName(Node\Name $name)
    {
        return call_user_func(
            $this->nameVisitor,
            $name
        );
    }
}