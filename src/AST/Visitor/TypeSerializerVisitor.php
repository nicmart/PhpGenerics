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
     *
     */
    const ATTR_CHANGED = "generictype_changed";

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
        $type = $node->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);

        if (!$type) {
            return new MaintainNode();
        }

        $name = $this->typeSerializer->serialize($type);
        $phpParserName = $this->phpNameAdapter->toPhpName($name);

        $this->nameTypeAdapter->withTypeName($node, $phpParserName);

        return new MaintainNode();

        $this->skipChildren($node);

        // Basically removes scalar typehints in PHP < 7
        if ($typeHintField = $this->typeHintField($node)) {
            $this->removeTypeHints($node, $typeHintField);
        }

        // Remove uses that are now associated to a non-reference type
        if ($node instanceof Node\Stmt\Use_) {
            $this->removeTypesInUses($node);
        }

        // Check if it is a node we have to process
        if (!$this->isValidNode($node)) {
            return new MaintainNode();
        }

        $type = $node->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);

        $name = $this->typeSerializer->serialize($type);
        $phpParserName = $this->phpNameAdapter->toPhpName($name);

        $this->setName($node, $phpParserName);

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
        if (!$useUse->name->hasAttribute(self::ATTR_CHANGED)
            || !$useUse->name->hasAttribute(TypeAnnotatorVisitor::ATTR_NAME)
        ) {
            return [new Node\Stmt\Use_([$useUse])];
        }

        $type = $useUse->name->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);

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
     * @param Node $node
     * @param Node\Name $name
     * @return Node\Name|Node\Name\FullyQualified|string
     */
    private function setName(Node &$node, Node\Name $name)
    {
        if ($node instanceof Node\Name) {
            $name->setAttribute(
                self::ATTR_CHANGED,
                true
            );
            $name->setAttribute(
                TypeAnnotatorVisitor::ATTR_NAME,
                $node->getAttribute(
                    TypeAnnotatorVisitor::ATTR_NAME
                )
            );
            return $node = $name;
        }

        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Interface_) {
            return $node->name = $name->getLast();
        }
    }
    
    /**
     * @param Node $node
     */
    private function skipChildren(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            return $this->skip($node->name);
        }
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function isValidNode(Node $node)
    {
        return $node->hasAttribute(TypeAnnotatorVisitor::ATTR_NAME)
            && !$node->hasAttribute(self::ATTR_SKIP)
        ;
    }

    /**
     * @param Node $node
     */
    private function skip(Node $node)
    {
        $node->setAttribute(
            self::ATTR_SKIP,
            true
        );
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
     * @param $paramOrFunction
     * @param $field
     */
    private function removeTypeHints($paramOrFunction, $field)
    {
        if (!$paramOrFunction->$field instanceof Node\Name) {
            return;
        }

        $type = $paramOrFunction->$field->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);

        if ($this->hasTypeToBeErased($type)) {
            $paramOrFunction->$field = null;
        }
    }

    /**
     * @param Node\Stmt\Use_ $use
     */
    private function removeTypesInUses(Node\Stmt\Use_ $use)
    {
        foreach ($use->uses as $i => $useUse) {
            $type = $useUse->name->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);
            if ($type instanceof PrimitiveType || $this->hasTypeToBeErased($type)) {
                unset($use->uses[$i]);
            }
        }
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function typeHintField(Node $node)
    {
        if ($node instanceof Node\Param) {
            return "type";
        }

        if ($node instanceof Node\FunctionLike) {
            return "returnType";
        }

        return null;
    }
}