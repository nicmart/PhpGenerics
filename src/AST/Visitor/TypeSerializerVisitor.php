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


use NicMart\Generics\AST\NodesList;
use NicMart\Generics\AST\Type\NodeNameTypeAdapter;
use NicMart\Generics\AST\Visitor\Action\LeaveNodeAction;
use NicMart\Generics\AST\Visitor\Action\MaintainNode;
use NicMart\Generics\AST\Visitor\Action\RemoveNode;
use NicMart\Generics\AST\Visitor\Action\ReplaceNodeWith;
use NicMart\Generics\AST\Visitor\Action\ReplaceNodeWithList;
use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\Type;
use PhpParser\Node;

/**
 * Class TypeSerializerVisitor
 * @package NicMart\Generics\AST\Visitor
 */
class TypeSerializerVisitor implements Visitor
{
    /**
     *
     */
    const ATTR_SKIP = "generictype_skip";

    /**
     * @var TypeSerializer
     */
    private $typeSerializer;

    /**
     * @var PhpNameAdapter
     */
    private $phpNameAdapter;

    /**
     * @var NodeNameTypeAdapter
     */
    private $nameTypeAdapter;

    /**
     * TypeSerializerVisitor constructor.
     * @param TypeSerializer $typeSerializer
     * @param PhpNameAdapter $phpNameAdapter
     * @param NodeNameTypeAdapter $nameTypeAdapter
     */
    public function __construct(
        TypeSerializer $typeSerializer,
        PhpNameAdapter $phpNameAdapter,
        NodeNameTypeAdapter $nameTypeAdapter
    ) {
        $this->typeSerializer = $typeSerializer;
        $this->phpNameAdapter = $phpNameAdapter;
        $this->nameTypeAdapter = $nameTypeAdapter;
    }

    /**
     * @param Node $node
     * @return MaintainNode|ReplaceNodeWith
     */
    public function enterNode(Node $node)
    {
        // Remove uses that are now associated to a non-reference type
        if ($node instanceof Node\Stmt\Use_) {
            $this->removeTypesInUses($node);
        }

        $type = $node->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);

        if (!$type) {
            return new MaintainNode();
        }

        $name = $this->typeSerializer->serialize($type);
        $phpParserName = $this->phpNameAdapter->toPhpName($name);
        $node = $this->nameTypeAdapter->withTypeName($node, $phpParserName);

        // Basically removes scalar typehints in PHP < 7
        if ($this->isTypeHintNode($node)) {
            $this->removeTypeHints($node);
        }

        return new ReplaceNodeWith($node);
    }

    /**
     * @param Node $node
     * @return LeaveNodeAction
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Use_) {
            return new MaintainNode();
        }

        $uses = [];
        foreach ($node->uses as $useUse) {
            $uses = array_merge($uses, $this->expandUseUse($useUse));
        }

        return new ReplaceNodeWithList(new NodesList($uses));
    }

    /**
     * @param Node\Stmt\UseUse $useUse
     * @return Node\Stmt\Use_[]
     */
    private function expandUseUse(Node\Stmt\UseUse $useUse)
    {
        if (!$useUse->hasAttribute(TypeAnnotatorVisitor::ATTR_NAME)
        ) {
            return [new Node\Stmt\Use_([$useUse])];
        }

        $type = $useUse->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);

        $subTypes = $type->bottomUpFold(
            [], function (array $z, Type $t) {
                $z[] = $t;
                return $z;
            }
        );

        $uses = [];
        foreach ($subTypes as $subType) {
            if ($subType instanceof PrimitiveType) continue;
            $uses[] = new Node\Stmt\Use_([
                new Node\Stmt\UseUse(
                    $this->phpNameAdapter->toPhpName(
                        $this->typeSerializer->serialize($subType)
                    )
                )
            ]);
        }

        return $uses;
    }

    /**
     * @param Type $type
     * @return bool
     */
    private function hasTypeToBeErased(Type $type)
    {
        if ($type instanceof PrimitiveType) {
            return !$type->isSupportedType();
        }

        return false;
    }

    /**
     * @param Node $typeHintNode
     * @internal param $paramOrFunction
     * @internal param $field
     */
    private function removeTypeHints(Node $typeHintNode)
    {
        $type = $typeHintNode->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);

        if ($this->hasTypeToBeErased($type)) {
            $this->removeTypeHintSubNode($typeHintNode);
        }
    }

    /**
     * @param Node\Stmt\Use_ $use
     */
    private function removeTypesInUses(Node\Stmt\Use_ $use)
    {
        foreach ($use->uses as $i => $useUse) {

            $type = $useUse->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);
            if ($type instanceof PrimitiveType || $this->hasTypeToBeErased($type)) {
                unset($use->uses[$i]);
            }

        }
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function isTypeHintNode(Node $node)
    {
        if ($node instanceof Node\Param) {
            return true;
        }

        if ($node instanceof Node\FunctionLike) {
            return true;
        }

        return false;
    }

    private function removeTypeHintSubNode(Node $node)
    {
        if ($node instanceof Node\Param) {
            $node->type = null;
        }

        if ($node instanceof Node\FunctionLike) {
            $node->returnType = null;
        }
    }
}