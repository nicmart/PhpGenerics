<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor\Visitor;

use NicMart\Generics\AST\Visitor\Action\EnterNodeAction;
use NicMart\Generics\AST\Visitor\Action\LeaveNodeAction;
use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\AST\Visitor\Visitor;
use NicMart\Generics\Infrastructure\PhpDocumentor\TypeDocBlockTransformer;
use PhpParser\Node;

/**
 * Class PhpDocTypeTransformerVisitor
 * @package NicMart\Generics\Infrastructure\PhpDocumentor\Visitor
 */
class PhpDocTypeTransformerVisitor implements Visitor
{
    /**
     * @var TypeDocBlockTransformer
     */
    private $transformer;

    /**
     * PhpDocTypeTransformerVisitor constructor.
     * @param TypeDocBlockTransformer $transformer
     */
    public function __construct(TypeDocBlockTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param Node $node
     * @return EnterNodeAction
     */
    public function enterNode(Node $node)
    {
        if (!$node->hasAttribute(PhpDocTypeAnnotatorVisitor::ATTR_NAME)) {
            return new MaintainNode();
        }

        $node->setAttribute(
            PhpDocTypeAnnotatorVisitor::ATTR_NAME,
            $this->transformer->transform(
                $node->getAttribute(PhpDocTypeAnnotatorVisitor::ATTR_NAME)
            )
        );

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
}