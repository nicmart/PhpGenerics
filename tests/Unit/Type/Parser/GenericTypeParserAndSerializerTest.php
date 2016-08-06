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

use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\GenericNameApplication;
use NicMart\Generics\Name\Generic\Parser\GenericTypeNameParser;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\VariableType;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class GenericTypeParserTest
 * @package NicMart\Generics\Type\Parser
 */
class GenericTypeParserAndSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericTypeParserAndSerializer|PHPUnit_Framework_MockObject_MockObject
     */
    private $parser;

    /**
     * @var GenericTypeNameParser|PHPUnit_Framework_MockObject_MockObject
     */
    private $nameParser;

    public function setUp()
    {
        $this->nameParser = $this->getMock(
            '\NicMart\Generics\Name\Generic\Parser\GenericTypeNameParser'
        );

        $this->parser = new GenericTypeParserAndSerializer($this->nameParser);
    }

    public function testParsePrimitive()
    {
        $this->assertEquals(
            new PrimitiveType(FullName::fromString("string")),
            $this->parser->parse(
                RelativeName::fromString("string"),
                NamespaceContext::fromNamespaceName("A\\B")
            )
        );

        $this->assertEquals(
            new PrimitiveType(FullName::fromString("callable")),
            $this->parser->parse(
                RelativeName::fromString("callable"),
                NamespaceContext::fromNamespaceName("A\\B")
            )
        );
    }

    public function testParseVariableType()
    {
        $context = NamespaceContext::fromNamespaceName('\NicMart\Generics\Variable');

        $varName = RelativeName::fromString("T");

        $this->assertEquals(
            new VariableType(FullName::fromString('\NicMart\Generics\Variable\T')),
            $this->parser->parse(
                $varName,
                $context
            )
        );
    }

    public function testParseGenericType()
    {
        $context = NamespaceContext::fromNamespaceName('\NicMart\Generics\Variable');

        $typeName = FullName::fromString("MyGen«T·S»");

        $this->nameParser
            ->expects($this->once())
            ->method("parse")
            ->with($typeName)
            ->willReturn($typeApplication = new GenericNameApplication(
                FullName::fromString("MyGen"), array(
                    RelativeName::fromString("T"),
                    RelativeName::fromString("S"),
                )
            ))
        ;

        $this->nameParser
            ->expects($this->once())
            ->method("isGeneric")
            ->with($typeName)
            ->willReturn(true)
        ;

        $this->assertEquals(
            new GenericType(
                FullName::fromString("MyGen"), array(
                    new VariableType(FullName::fromString('\NicMart\Generics\Variable\T')),
                    new VariableType(FullName::fromString('\NicMart\Generics\Variable\S')),
                )
            ),
            $this->parser->parse(
                $typeName,
                $context
            )
        );
    }

    public function testParseParametrizedType()
    {
        $context = NamespaceContext::fromNamespaceName('\NicMart');

        $typeName = FullName::fromString("MyGen«Foo·Bar»");

        $this->nameParser
            ->expects($this->once())
            ->method("parse")
            ->with($typeName)
            ->willReturn($typeApplication = new GenericNameApplication(
                FullName::fromString("MyGen"), array(
                    RelativeName::fromString("Foo"),
                    RelativeName::fromString("Bar"),
                )
            ))
        ;

        $this->nameParser
            ->expects($this->exactly(3))
            ->method("isGeneric")
            ->withConsecutive(
                array($typeName),
                array(FullName::fromString("NicMart\\Foo")),
                array(FullName::fromString("NicMart\\Bar"))
            )
            ->willReturnOnConsecutiveCalls(true, false, false)
        ;

        $this->assertEquals(
            new ParametrizedType(
                FullName::fromString("MyGen"), array(
                    new SimpleReferenceType(FullName::fromString('\NicMart\Foo')),
                    new SimpleReferenceType(FullName::fromString('\NicMart\Bar')),
                )
            ),
            $this->parser->parse(
                $typeName,
                $context
            )
        );
    }

    public function testParseSimpleReferenceType()
    {
        $context = NamespaceContext::fromNamespaceName('\NicMart');

        $name = RelativeName::fromString("Foo");

        $this->nameParser
            ->expects($this->once())
            ->method("isGeneric")
            ->with(FullName::fromString('\NicMart\Foo'))
            ->willReturn(false)
        ;

        $this->assertEquals(
            new SimpleReferenceType(FullName::fromString('\NicMart\Foo')),
            $this->parser->parse(
                $name,
                $context
            )
        );
    }

    public function testSerialize()
    {
         $type = new ParametrizedType(
            FullName::fromString("NicMart\\MyGen"), array(
                new SimpleReferenceType(FullName::fromString('\NicMart\Foo')),
                new SimpleReferenceType(FullName::fromString('\NicMart\Bar')),
            )
        );

        $this->nameParser
            ->expects($this->once())
            ->method("serialize")
            ->with(
                new GenericNameApplication(
                    FullName::fromString('NicMart\MyGen'), array(
                        FullName::fromString('NicMart\Foo'),
                        FullName::fromString('NicMart\Bar')
                    )
                )
            )
            ->willReturn(FullName::fromString("NicMart\\MyGen«Foo·Bar»"))
        ;

        $this->assertEquals(
            FullName::fromString("NicMart\\MyGen«Foo·Bar»"),
            $this->parser->serialize($type)
        );
    }
}
