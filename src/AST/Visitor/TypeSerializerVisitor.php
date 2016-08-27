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
use NicMart\Generics\AST\Visitor\Action\RemoveNode;
use NicMart\Generics\AST\Visitor\Action\ReplaceNodeWith;
use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\Serializer\TypeSerializer;
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

    private $isPHP7 = false;

    /**
     * TypeSerializerVisitor constructor.
     * @param TypeSerializer $typeSerializer
     * @param PhpNameAdapter $phpNameAdapter
     */
    public function __construct(
        TypeSerializer $typeSerializer,
        PhpNameAdapter $phpNameAdapter
    ) {
        $this->typeSerializer = $typeSerializer;
        $this->phpNameAdapter = $phpNameAdapter;

        $this->isPHP7 = version_compare(phpversion(), '7.0.0', '>=');
    }

    /**
     * @param Node $node
     * @return MaintainNode|ReplaceNodeWith
     */
    public function enterNode(Node $node)
    {
        $this->skipChildren($node);

        if ($node instanceof Node\Param && !$this->isPHP7) {
            // PHP < 7
            $this->removePrimitiveTypeHints($node);
        }

        if ($node instanceof Node\Stmt\Use_) {
            $this->removePrimitiveTypeUsages($node);
        }

        if (!$this->isValidNode($node)) {
            return new MaintainNode();
        }

        $type = $node->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);

        $name = $this->typeSerializer->serialize($type);

        $this->setName($node, $name);

        return new ReplaceNodeWith($node);
    }

    /**
     * @param Node $node
     * @return MaintainNode
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\UseUse) {
            if ($node->name->hasAttribute(self::ATTR_CHANGED)) {
                $node->alias = $node->name->getLast();
            }
        }

        return new MaintainNode();
    }

    /**
     * @param Node $node
     * @param Name $name
     * @return Node\Name|Node\Name\FullyQualified|string
     */
    private function setName(Node &$node, Name $name)
    {
        if ($node instanceof Node\Name) {
            $newName = $this->phpNameAdapter->toPhpName($name);
            $newName->setAttribute(
                self::ATTR_CHANGED,
                true
            );
            return $node = $newName;
        }

        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Interface_) {
            return $node->name = $name->last()->toString();
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

    private function removePrimitiveTypeHints(Node\Param $param)
    {
        if (!$param->type instanceof Node\Name) {
            return;
        }
        
        $type = $param->type->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);

        if ($type instanceof PrimitiveType) {
            $param->type = null;
        }
    }

    private function removePrimitiveTypeUsages(Node\Stmt\Use_ $use)
    {
        foreach ($use->uses as $i => $useUse) {
            $type = $useUse->name->getAttribute(TypeAnnotatorVisitor::ATTR_NAME);
            if ($type instanceof PrimitiveType) {
                unset($use->uses[$i]);
            }
        }
    }
}