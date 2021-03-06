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

use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Type\Parser\TypeParser;
use PhpParser\Node;

/**
 * Class TypeAnnotatorVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class TypeAnnotatorVisitor implements Visitor
{
    const ATTR_NAME = "generictype";

    /**
     * @var NamespaceContext
     */
    private $namespaceContext;

    /**
     * @var PhpNameAdapter
     */
    private $phpNameAdapter;

    /**
     * @var TypeParser
     */
    private $typeParser;

    /**
     * TypeAnnotatorVisitor constructor.
     * @param TypeParser $typeParser
     * @param NamespaceContext $namespaceContext
     * @param PhpNameAdapter $phpNameAdapter
     */
    public function __construct(
        TypeParser $typeParser,
        NamespaceContext $namespaceContext,
        PhpNameAdapter $phpNameAdapter
    ) {
        $this->namespaceContext = $namespaceContext;
        $this->phpNameAdapter = $phpNameAdapter;
        $this->typeParser = $typeParser;
    }

    /**
     * @param Node $node
     * @return MaintainNode
     */
    public function enterNode(Node $node)
    {
        $this->overrideChildrenNames($node);

        if (!$this->isTypeNode($node)) {
            return new MaintainNode();
        }

        $node->setAttribute(
            self::ATTR_NAME,
            $this->typeParser->parse(
                $this->extractName($node),
                $this->namespaceContext
            )
        );

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
     * @return Name
     */
    private function extractName(Node $node)
    {
        if ($node instanceof Node\Name) {
            return $this->phpNameAdapter->fromPhpName($node);
        }

        if ($this->isClass($node)) {
            /** @var Node\Stmt\Class_|Node\Stmt\Interface_ $node */
            return RelativeName::fromString($node->name);
        }
    }

    /**
     * In PhpParser there can be names that are neither Relative nor FullQualified
     *
     * This is a workaround for when that happens (Use statements)
     *
     * @param Node $node
     */
    private function overrideChildrenNames(Node $node)
    {
        if ($node instanceof Node\Stmt\UseUse) {
            $node->name = new Node\Name\FullyQualified($node->name->parts);
        }
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function isClass(Node $node)
    {
        return $node instanceof Node\Stmt\Class_
            || $node instanceof Node\Stmt\Interface_
        ;
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function isTypeNode(Node $node)
    {
        return $this->isClass($node)
            || $node instanceof Node\Name
        ;
    }
}