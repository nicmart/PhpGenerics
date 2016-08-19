<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 19/08/2016, 10:33
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Type\UnionType;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;

class TypeDocBlockSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        /** @var TypeSerializer|\PHPUnit_Framework_MockObject_MockObject $typeSerializer */
        $typeSerializer = $this->getMock(TypeSerializer::class);
        $typeSerializer
            ->expects($this->any())
            ->method("serialize")
            ->willReturnCallback(function (Type $type) {
                return $type->name();
            })
        ;

        $serializer = new TypeDocBlockSerializer(
            new TypeResolver(),
            $typeSerializer,
            $phpDocSerializer = new Serializer()
        );

        $docBlock = new DocBlock(
            "Summary",
            new DocBlock\Description("description"),
            array(
                new DocBlock\Tags\Author("john", "john@example.com"),
                new DocBlock\Tags\Param(
                    "foo",
                    new AnnotatedType(
                        new String_(), //This should be ignored
                        new SimpleReferenceType(FullName::fromString(FullName::class))
                    )
                ),
                new DocBlock\Tags\Param(
                    "bar",
                    new AnnotatedType(
                        new String_(), //This should be ignored
                        new UnionType([
                            new SimpleReferenceType(FullName::fromString(FullName::class)),
                            new SimpleReferenceType(FullName::fromString(RelativeName::class)),
                        ])

                    )
                ),
                new DocBlock\Tags\Return_(
                    new AnnotatedType(
                        new String_(), //This should be ignored
                        new PrimitiveType(FullName::fromString("float"))
                    )
                )
            )
        );

        $expectedDocBlock = new DocBlock(
            "Summary",
            new DocBlock\Description("description"),
            array(
                new DocBlock\Tags\Author("john", "john@example.com"),
                new DocBlock\Tags\Param(
                    "foo",
                    new Object_(new Fqsen("\\" . FullName::class))
                ),
                new DocBlock\Tags\Param(
                    "bar",
                    new Compound(array(
                        new Object_(new Fqsen("\\" . FullName::class)),
                        new Object_(new Fqsen("\\" . RelativeName::class)),
                    ))
                ),
                new DocBlock\Tags\Return_(
                    new Float_()
                )
            )
        );

        $this->assertEquals(
            $phpDocSerializer->getDocComment($expectedDocBlock),
            $serializer->serialize($docBlock)
        );
    }
}
