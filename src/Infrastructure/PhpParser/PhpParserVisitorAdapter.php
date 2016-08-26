<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser;


use NicMart\Generics\AST\Visitor\Action\DontTraverseChildren;
use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\AST\Visitor\Action\RemoveNode;
use NicMart\Generics\AST\Visitor\Action\ReplaceNodeWith;
use NicMart\Generics\AST\Visitor\Action\ReplaceNodeWithList;
use NicMart\Generics\AST\Visitor\Action\VisitorAction;
use NicMart\Generics\AST\Visitor\Visitor;
use PhpParser\Node;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;

/**
 * Class PhpParserVisitorAdapter
 * @package NicMart\Generics\Adapter
 */
class PhpParserVisitorAdapter extends NodeVisitorAbstract
{
    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * PhpParserVisitorAdapter constructor.
     * @param Visitor $visitor
     */
    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * @param Node $node
     * @return bool|null|Node|\PhpParser\Node[]
     */
    public function enterNode(Node $node)
    {
        return $this->actionToPhpParserResult(
            $this->visitor->enterNode($node)
        );
    }

    /**
     * @param Node $node
     * @return bool|null|Node|\PhpParser\Node
     */
    public function leaveNode(Node $node)
    {
        return $this->actionToPhpParserResult(
            $this->visitor->leaveNode($node)
        );
    }

    /**
     * @param VisitorAction $action
     * @return bool|null|Node|\PhpParser\Node[]
     */
    private function actionToPhpParserResult(VisitorAction $action)
    {
        if ($action instanceof MaintainNode) {
            return null;
        }

        if ($action instanceof RemoveNode) {
            return false;
        }

        if ($action instanceof ReplaceNodeWith) {
            return $action->node();
        }

        if ($action instanceof ReplaceNodeWithList) {
            return $action->nodeList()->nodes();
        }

        if ($action instanceof DontTraverseChildren) {
            return NodeTraverserInterface::DONT_TRAVERSE_CHILDREN;
        }
    }
}