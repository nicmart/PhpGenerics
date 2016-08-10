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


use NicMart\Generics\AST\Visitor\Action\EnterNodeAction;
use NicMart\Generics\AST\Visitor\Action\LeaveNodeAction;
use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use PhpParser\Node;

/**
 * Class FilteredVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class FilteredVisitor implements Visitor
{
    /**
     * @var callable
     */
    private $predicate;
    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @return \Closure
     */
    public static function namePredicate()
    {
        return function (Node $node) {
            return $node instanceof Node\Name;
        };
    }

    /**
     * FilteredVisitor constructor.
     * @param Visitor $visitor
     * @param callable $predicate
     */
    public function __construct(Visitor $visitor, $predicate)
    {
        $this->predicate = $predicate;
        $this->visitor = $visitor;
    }

    /**
     * @param Node $node
     * @return EnterNodeAction|MaintainNode
     */
    public function enterNode(Node $node)
    {
        if (!call_user_func($this->predicate, $node)) {
            return new MaintainNode();
        }

        return $this->visitor->enterNode($node);
    }

    /**
     * @param Node $node
     * @return LeaveNodeAction|MaintainNode
     */
    public function leaveNode(Node $node)
    {
        if (!call_user_func($this->predicate, $node)) {
            return new MaintainNode();
        }

        return $this->visitor->leaveNode($node);
    }
}