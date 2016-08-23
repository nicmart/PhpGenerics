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
use NicMart\Generics\Type\Transformer\TypeTransformer;
use PhpParser\Node;

/**
 * Class TypeTransformerVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class TypeTransformerVisitor implements Visitor
{
    /**
     * @var TypeTransformer
     */
    private $typeTransformer;

    /**
     * TypeTransformerVisitor constructor.
     * @param TypeTransformer $typeTransformer
     */
    public function __construct(TypeTransformer $typeTransformer)
    {
        $this->typeTransformer = $typeTransformer;
    }

    /**
     * @param Node $node
     * @return MaintainNode
     */
    public function enterNode(Node $node)
    {
        if ($node->hasAttribute(TypeAnnotatorVisitor::ATTR_NAME)) {
            $node->setAttribute(
                TypeAnnotatorVisitor::ATTR_NAME,
                $this->typeTransformer->transform(
                    $node->getAttribute(TypeAnnotatorVisitor::ATTR_NAME)
                )
            );
        }

        return new MaintainNode();
    }

    /**
     * @param Node $node
     * @return MaintainNode
     */
    public function leaveNode(Node $node)
    {
        return new MaintainNode();
    }

}