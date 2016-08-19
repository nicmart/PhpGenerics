<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 19/08/2016, 12:41
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\Transformer\TypeTransformer;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Type\UnionType;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\String_;

class TypeDocBlockTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        /** @var TypeTransformer $typeTransformer */
        $typeTransformer = $this->getMock(TypeTransformer::class);

        $typeTransformer
            ->expects($this->any())
            ->method("transform")
            ->willReturnCallback(function (Type $type) {
                if ($type instanceof UnionType) {
                    return new SimpleReferenceType(
                        FullName::fromString("Union")
                    );
                }
                return new SimpleReferenceType(
                    $type->name()->last()->toRelativeName()->toFullName()
                );
            })
        ;

        $transformer = new TypeDocBlockTransformer($typeTransformer);

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
                    new AnnotatedType(
                        new String_(), //This should be ignored
                        new SimpleReferenceType(FullName::fromString("FullName"))
                    )
                ),
                new DocBlock\Tags\Param(
                    "bar",
                    new AnnotatedType(
                        new String_(), //This should be ignored
                        new SimpleReferenceType(FullName::fromString("Union"))
                    )
                ),
                new DocBlock\Tags\Return_(
                    new AnnotatedType(
                        new String_(), //This should be ignored
                        new SimpleReferenceType(FullName::fromString("float"))
                    )
                )
            )
        );

        $this->assertEquals(
            $expectedDocBlock,
            $transformer->transform($docBlock)
        );
    }
}
