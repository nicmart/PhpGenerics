<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;


use NicMart\Generics\Infrastructure\PhpDocumentor\Type\AnnotatedType;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\UnionType;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit_Framework_MockObject_MockObject;

class TypeAnnotatorTagFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TagFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $tagFactory;

    /**
     * @var TypeParser|PHPUnit_Framework_MockObject_MockObject
     */
    private $typeParser;

    public function setUp()
    {
        $this->tagFactory = $this->getMock(TagFactory::class);
        $this->typeParser = $this->getMock(TypeParser::class);
    }

    public function testCreateParam()
    {
        $fullNameClass = FullName::class;
        $tagLine = "@param $fullNameClass \$varname";

        $namespaceContext = NamespaceContext::fromNamespaceName("NicMart");
        $phpDocContext = new Context("NicMart");

        $tagFactory = $this->tagFactory;

        $tagFactory
            ->expects($this->once())
            ->method('create')
            ->with($tagLine, $phpDocContext)
            ->willReturn(
                new Param(
                    "varname",
                    new Object_(new Fqsen("\\" . $fullNameClass))
                )
            )
        ;

        $typeParser = $this->typeParser;
        $typeParser
            ->expects($this->once())
            ->method("parse")
            ->with(
                FullName::fromString($fullNameClass),
                $namespaceContext
            )
            ->willReturn(
                $domainType = new SimpleReferenceType(FullName::fromString($fullNameClass))
            )
        ;

        $factory = new TypeAnnotatorTagFactory(
            $tagFactory,
            $typeParser
        );

        $tag = $factory->create(
            $tagLine,
            $phpDocContext
        );

        $this->assertInstanceOf(
            Param::class,
            $tag
        );

        /** @var AnnotatedType $annotatedPhpdocType */
        $annotatedPhpdocType = $tag->getType();

        $this->assertInstanceOf(
            AnnotatedType::class,
            $annotatedPhpdocType
        );

        $this->assertEquals(
            $domainType,
            $annotatedPhpdocType->type()
        );
    }

    public function testCreateParamCompound()
    {
        $fullNameClass = FullName::class;
        $tagLine = "@param $fullNameClass|string \$varname";

        $namespaceContext = NamespaceContext::fromNamespaceName("NicMart");
        $phpDocContext = new Context("NicMart");

        $tagFactory = $this->tagFactory;

        $tagFactory
            ->expects($this->once())
            ->method('create')
            ->with($tagLine, $phpDocContext)
            ->willReturn(
                new Param(
                    "varname",
                    new Compound(array(
                        $phpDocType1 = new Object_(new Fqsen("\\" . $fullNameClass)),
                        $phpDocType2 = new String_()
                    ))
                )
            )
        ;

        $typeParser = $this->typeParser;
        $typeParser
            ->expects($this->exactly(2))
            ->method("parse")
            ->withConsecutive(
                array(
                    FullName::fromString($fullNameClass),
                    $namespaceContext
                ),
                array(
                    FullName::fromString("string"),
                    $namespaceContext
                )
            )
            ->willReturnOnConsecutiveCalls(
                $type1 = new SimpleReferenceType(FullName::fromString($fullNameClass)),
                $type2 = new PrimitiveType(FullName::fromString("string"))
            )
        ;

        $domainType = new UnionType(array($type1, $type2));

        $factory = new TypeAnnotatorTagFactory(
            $tagFactory,
            $typeParser
        );

        $tag = $factory->create(
            $tagLine,
            $phpDocContext
        );

        $this->assertInstanceOf(
            Param::class,
            $tag
        );

        /** @var AnnotatedType $annotatedPhpdocType */
        $annotatedPhpdocType = $tag->getType();

        $this->assertInstanceOf(
            AnnotatedType::class,
            $annotatedPhpdocType
        );

        $this->assertEquals(
            $domainType,
            $annotatedPhpdocType->type()
        );

        /** @var Compound $compoundPhpDocType */
        $compoundPhpDocType = $annotatedPhpdocType->phpDocType();

        $this->assertInstanceOf(
            AnnotatedType::class,
            $compoundPhpDocType->get(0)
        );

        $this->assertEquals(
            $type1,
            $compoundPhpDocType->get(0)->type()
        );

        $this->assertEquals(
            $type2,
            $compoundPhpDocType->get(1)->type()
        );
    }
}
