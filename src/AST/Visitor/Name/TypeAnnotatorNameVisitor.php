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
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\Infrastructure\PhpParser\PrettyPrinter;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Type\Parser\TypeParser;
use PhpParser\Node;
use PhpParser\NodeDumper;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class TypeAnnotatorNameVisitor
 *
 * Using a type parser, add the parser type as an attribute to the given
 * php-parser name.
 *
 * @package NicMart\Generics\AST\Visitor\Name
 */
final class TypeAnnotatorNameVisitor implements NameVisitor
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
     * @return EnterNodeAction
     */
    public function visitName(Node\Name $name)
    {
        $this->assertValidContext($name);

        $name->setAttribute(
            self::ATTR_NAME,
            $this->typeParser->parse(
                $this->fromPhpNameToName($name),
                $name->getAttribute(NamespaceContextVisitor::ATTR_NAME)
            )
        );

        return new ReplaceNodeWith($name);
    }

    /**
     * @param Node\Name $node
     */
    private function assertValidContext(Node\Name $node)
    {
        if (!$node->hasAttribute(NamespaceContextVisitor::ATTR_NAME)) {
            $dumper = new NodeDumper();
            throw new \RuntimeException(sprintf(
                "Namespace context not found in node. Node Content:\n%s",
                $dumper->dump($node)
            ));
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