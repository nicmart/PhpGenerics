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


use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Type\Parser\TypeParser;
use PhpParser\Node;

/**
 * Class TypeAnnotatorNameVisitor
 *
 * Using a type parser, add the parser type as an attribute to the given
 * php-parser name.
 *
 * @package NicMart\Generics\AST\Visitor\Name
 */
final class TypeAnnotatorNameVisitor
{
    /**
     *
     */
    const ATTR_NAME = "type";

    /**
     * @var TypeParser
     */
    private $typeParser;

    /**
     * TypeAnnotatorNameVisitor constructor.
     * @param TypeParser $typeParser
     */
    public function __construct(TypeParser $typeParser)
    {
        $this->typeParser = $typeParser;
    }

    /**
     * @param Node\Name $name
     * @return Node\Name
     */
    public function __invoke(Node\Name $name)
    {
        $this->assertValidContext($name);

        $name->setAttribute(
            self::ATTR_NAME,
            $this->typeParser->parse(
                $this->fromPhpNameToName($name),
                $name->getAttribute(NamespaceContextVisitor::ATTR_NAME)
            )
        );

        return $name;
    }

    /**
     * @param Node\Name $node
     */
    private function assertValidContext(Node\Name $node)
    {
        if (!$node->hasAttribute(NamespaceContextVisitor::ATTR_NAME)) {
            throw new \RuntimeException("Namespace context not found in node");
        }
    }

    /**
     * @param Node\Name $name
     * @return Name
     */
    private function fromPhpNameToName(Node\Name  $name)
    {
        if ($name->isFullyQualified()) {
            return new FullName($name->parts);
        }

        return new RelativeName($name->parts);
    }
}