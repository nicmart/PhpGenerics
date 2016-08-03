<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor\Name;

use NicMart\Generics\AST\Visitor\Action\EnterNodeAction;
use NicMart\Generics\AST\Visitor\Action\ReplaceNodeWith;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use PhpParser\Node;

/**
 * Class TypeSerializerNameVisitor
 * @package NicMart\Generics\AST\Visitor\Name
 */
final class TypeSerializerNameVisitor implements NameVisitor
{
    /**
     * @var TypeSerializer
     */
    private $typeSerializer;

    /**
     * TypeSerializerNameVisitor constructor.
     * @param TypeSerializer $typeSerializer
     */
    public function __construct(TypeSerializer $typeSerializer)
    {
        $this->typeSerializer = $typeSerializer;
    }

    /**
     * @param Node\Name $name
     * @return EnterNodeAction
     */
    public function visitName(Node\Name $name)
    {
        $this->assertValidAttr($name);

        return new ReplaceNodeWith($this->fromNameToPhpName(
            $this->typeSerializer->serialize(
                $name->getAttribute(
                    TypeAnnotatorNameVisitor::ATTR_NAME
                )
            )
        ));
    }

    /**
     * @todo isNative after refactor should be removed I guess
     * @param Name $name
     * @return Node\Name
     */
    private function fromNameToPhpName(Name $name)
    {
        return $name instanceof FullName && !$name->isNative()
            ? new Node\Name\FullyQualified($name->parts())
            : new Node\Name($name->parts())
        ;
    }

    /**
     * @param Node\Name $node
     */
    private function assertValidAttr(Node\Name $node)
    {
        if (!$node->hasAttribute(TypeAnnotatorNameVisitor::ATTR_NAME)) {
            throw new \RuntimeException("Type not found in node");
        }
    }
}