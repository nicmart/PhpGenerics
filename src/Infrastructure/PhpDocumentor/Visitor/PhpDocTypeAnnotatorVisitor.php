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
use phpDocumentor\Reflection\Types\Context as PhpDocContext;
use NicMart\Generics\AST\Visitor\Visitor;
use phpDocumentor\Reflection\DocBlockFactory;
use PhpParser\Node;

/**
 * Class PhpDocTypeAnnotatorVisitor
 * @package NicMart\Generics\Infrastructure\PhpDocumentor\Visitor
 */
class PhpDocTypeAnnotatorVisitor implements Visitor
{
    const ATTR_NAME = "PhpDoc";

    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var PhpDocContext
     */
    private $phpDocContext;

    /**
     * PhpDocTypeAnnotatorVisitor constructor.
     * @param DocBlockFactory $docBlockFactory
     * @param PhpDocContext $phpDocContext
     */
    public function __construct(
        DocBlockFactory $docBlockFactory,
        PhpDocContext $phpDocContext
    ) {
        $this->docBlockFactory = $docBlockFactory;
        $this->phpDocContext = $phpDocContext;
    }


    /**
     * @param Node $node
     * @return EnterNodeAction
     */
    public function enterNode(Node $node)
    {
        $comment = $node->getDocComment();
        if (!$comment) {
            return new MaintainNode();
        }
        
        $node->setAttribute(
            self::ATTR_NAME,
            $this->docBlockFactory->create(
                $comment->getText(),
                $this->phpDocContext
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