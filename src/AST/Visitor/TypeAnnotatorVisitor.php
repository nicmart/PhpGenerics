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

use NicMart\Generics\AST\Type\NodeNameTypeAdapter;
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
     * @var NodeNameTypeAdapter
     */
    private $nameTypeAdapter;

    /**
     * TypeAnnotatorVisitor constructor.
     * @param TypeParser $typeParser
     * @param NamespaceContext $namespaceContext
     * @param PhpNameAdapter $phpNameAdapter
     * @param NodeNameTypeAdapter $nameTypeAdapter
     */
    public function __construct(
        TypeParser $typeParser,
        NamespaceContext $namespaceContext,
        PhpNameAdapter $phpNameAdapter,
        NodeNameTypeAdapter $nameTypeAdapter
    ) {
        $this->namespaceContext = $namespaceContext;
        $this->phpNameAdapter = $phpNameAdapter;
        $this->typeParser = $typeParser;
        $this->nameTypeAdapter = $nameTypeAdapter;
    }

    /**
     * @param Node $node
     * @return MaintainNode
     */
    public function enterNode(Node $node)
    {
        $name = $this->nameTypeAdapter->typeNameOf($node);

        if (!$name) {
            return new MaintainNode();
        }

        $this->annotateWithType($node, $name);
        $this->annotateExtendsAndImplements($node);

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
     * @param Node\Name $typeName
     */
    private function annotateWithType(
        Node $node,
        Node\Name $typeName
    ) {
        $node->setAttribute(
            self::ATTR_NAME,
            $t = $this->typeParser->parse(
                $this->phpNameAdapter->fromPhpName($typeName),
                $this->namespaceContext
            )
        );
    }

    /**
     * @param Node $node
     */
    private function annotateExtendsAndImplements(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            foreach ($node->implements as $implement) {
                $this->annotateWithType($implement, $implement);
            }
        }

        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Interface_) {
            foreach ((array) $node->extends as $extend) {
                $this->annotateWithType($extend, $extend);
            }
        }
    }
}