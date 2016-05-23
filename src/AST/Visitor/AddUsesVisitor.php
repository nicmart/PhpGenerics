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
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use PhpParser\Node;

/**
 * Class AddUsesVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class AddUsesVisitor implements Visitor
{
    /**
     * @var Uses
     */
    private $uses;

    /**
     * AddUsesVisitor constructor.
     * @param Uses $uses
     */
    public function __construct(Uses $uses)
    {
        $this->uses = $uses;
    }

    /**
     * @param Node $node
     * @return LeaveNodeAction
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Namespace_) {
            return new MaintainNode();
        }

        $children =& $node->stmts;
        $uses = new Uses();

        foreach ($children as $i => $node) {
            if (!$node instanceof Node\Stmt\Use_) {
                $children = array_slice($children, $i);
                break;
            }

            foreach ($node->uses as $use) {
                $uses = $uses->withUse(
                    Use_::fromStrings($use->name, $use->alias)
                );
            }
        }

        $uses = $uses->merge($this->uses);

        $usesNodes = array();

        foreach ($uses->getUsesByAliases() as $use) {
            $usesNodes[] = $this->getUseNode($use);
        }

        $children = array_merge($usesNodes, $children);

        return new MaintainNode();
    }

    /**
     * @param Node $node
     * @return EnterNodeAction
     */
    public function enterNode(Node $node)
    {
        return new MaintainNode();
    }

    /**
     * @param Use_ $use
     * @return Node\Stmt\Use_
     */
    private function getUseNode(Use_ $use)
    {
        return new Node\Stmt\Use_(array(new Node\Stmt\UseUse(
            new Node\Name($use->name()->parts()),
            $use->alias()->toString()
        )));
    }
}