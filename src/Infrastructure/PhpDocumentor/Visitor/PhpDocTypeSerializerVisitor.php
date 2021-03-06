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
use NicMart\Generics\Infrastructure\PhpDocumentor\Adapter\PhpDocContextAdapter;
use NicMart\Generics\Infrastructure\PhpDocumentor\DocBlockTagFunctor;
use NicMart\Generics\Infrastructure\PhpDocumentor\TypeDocBlockSerializer;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use phpDocumentor\Reflection\Types\Context as PhpDocContext;

class PhpDocTypeSerializerVisitor implements Visitor
{
    /**
     * @var TypeDocBlockSerializer
     */
    private $typeDocBlockSerializer;

    /**
     * @var PhpDocContext
     */
    private $phpDocContext;

    /**
     * PhpDocTypeSerializerVisitor constructor.
     * @param TypeDocBlockSerializer $typeDocBlockSerializer
     * @param PhpDocContext $phpDocContext
     */
    public function __construct(
        TypeDocBlockSerializer $typeDocBlockSerializer,
        PhpDocContext $phpDocContext
    ) {
        $this->typeDocBlockSerializer = $typeDocBlockSerializer;
        $this->phpDocContext = $phpDocContext;
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

        /** @var DocBlock $docBlock */
        $docBlock = $node->getAttribute(PhpDocTypeAnnotatorVisitor::ATTR_NAME);

        $docBlock = new DocBlock(
            $docBlock->getSummary(),
            $docBlock->getDescription(),
            $docBlock->getTags(),
            $this->phpDocContext,
            $docBlock->getLocation(),
            $docBlock->isTemplateStart(),
            $docBlock->isTemplateEnd()
        );
        
        $node->getDocComment()->setText(
            $this->typeDocBlockSerializer->serialize($docBlock)
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