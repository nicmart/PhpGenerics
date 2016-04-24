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

use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\AST\PhpDoc\PhpDocTransformer;
use PhpParser\Comment\Doc;
use PhpParser\Node;

/**
 * Class PhpDocTransformerVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class PhpDocTransformerVisitor implements Visitor
{
    /**
     * @var PhpDocTransformer
     */
    private $phpDocTransformer;

    /**
     * PhpDocTransformerVisitor constructor.
     * @param PhpDocTransformer $phpDocTransformer
     */
    public function __construct(PhpDocTransformer $phpDocTransformer)
    {
        $this->phpDocTransformer = $phpDocTransformer;
    }

    /**
     * @param Node $node
     * @return MaintainNode
     */
    public function enterNode(Node $node)
    {
        $this->transformPhpDoc($node);

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

    /**
     * @param Node $node
     * @return Node
     */
    private function transformPhpDoc(Node $node)
    {
        $doc = $this->getPhpDoc($node);

        if (!$doc) {
            return $node;
        }

        $this->setPhpDoc(
            $node,
            $this->phpDocTransformer->transform(
                $this->getPhpDoc($node),
                $node->getAttribute(NamespaceContextVisitor::ATTR_NAME)
            )
        );

        return $node;
    }

    /**
     * @param Node $node
     * @return null
     */
    private function getPhpDoc(Node $node)
    {
        $comments = $node->getAttribute("comments", array());

        if (!$comments) {
            return null;
        }

        $lastComment = $comments[count($comments) - 1];

        if ($lastComment instanceof Doc) {
            return $lastComment;
        }

        return null;
    }

    /**
     * @param Node $node
     * @param Doc $doc
     */
    private function setPhpDoc(Node $node, Doc $doc)
    {
        $comments = $node->getAttribute("comments", array());

        $comments[count($comments) - 1] = $doc;

        $node->setAttribute("comments", $comments);
    }
}