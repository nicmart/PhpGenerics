<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Parser;

use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\VariableType;

/**
 * Class GenericTypeParserTest
 * @package NicMart\Generics\Type\Parser
 */
class GenericTypeParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParsePrimitive()
    {
        $parser = new GenericTypeParser();

        $this->assertEquals(
            new PrimitiveType(FullName::fromString("string")),
            $parser->parse(
                RelativeName::fromString("string"),
                NamespaceContext::fromNamespaceName("A\\B")
            )
        );

        $this->assertEquals(
            new PrimitiveType(FullName::fromString("callable")),
            $parser->parse(
                RelativeName::fromString("callable"),
                NamespaceContext::fromNamespaceName("A\\B")
            )
        );
    }

    public function testParseVariableType()
    {
        $context = NamespaceContext::fromNamespaceName('\NicMart\Generics\Variable');

        $varName = RelativeName::fromString("T");

        $parser = new GenericTypeParser();

        $this->assertEquals(
            new VariableType(FullName::fromString('\NicMart\Generics\Variable\T')),
            $parser->parse(
                $varName,
                $context
            )
        );
    }
}
