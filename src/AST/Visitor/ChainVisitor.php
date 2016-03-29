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
use PhpParser\Node;

/**
 * Class ChainVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class ChainVisitor implements Visitor
{
    /**
     * @var Visitor[]
     */
    private $visitors;

    /**
     * ChainVisitor constructor.
     * @param Visitor[] $visitors
     */
    public function __construct(array $visitors)
    {
        foreach ($visitors as $visitor) {
            $this->addVisitor($visitor);
        }
    }

    public function enterNode(Node $node)
    {
        foreach ($this->visitors as $visitor)
    }

    public function leaveNode(Node $node)
    {
        // TODO: Implement leaveNode() method.
    }


    /**
     * @param Visitor $visitor
     */
    private function addVisitor(Visitor $visitor)
    {
        $this->visitors[] = $visitor;
    }
}