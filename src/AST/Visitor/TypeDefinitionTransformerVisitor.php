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
use NicMart\Generics\Name\Assignment\NameAssignment;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\SimpleName;
use NicMart\Generics\Name\Transformer\SimpleNameTransformer;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

/**
 * Class TypeDefinitionTransformerVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class TypeDefinitionTransformerVisitor implements Visitor
{
    /**
     * @var SimpleNameTransformer
     */
    private $nameTransformer;

    /**
     * TypeDefinitionTransformerVisitor constructor.
     * @param SimpleNameTransformer $nameTransformer
     */
    public function __construct(SimpleNameTransformer $nameTransformer)
    {
        $this->nameTransformer = $nameTransformer;
    }

    /**
     * @param Node $node
     * @return EnterNodeAction
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Class_ || $node instanceof Stmt\Interface_) {
            $node->name = $this->transformClassName(
                $node->name
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
     * @param string $className
     * @return string
     */
    private function transformClassName($className)
    {
        $from = new SimpleName($className);
        $to = $this->nameTransformer->transform($from);

        return $to->toString();
    }
}