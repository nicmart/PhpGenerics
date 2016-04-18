<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 18/04/2016, 17:29
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Transformer;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

class ByFullNameNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_qualifies_name_before_transforming()
    {
        $context = NamespaceContext::fromNamespaceName("Ns");
        $name = new RelativeName(array("foo"));

        $fullNameTransformer = $this->getMock(
            '\NicMart\Generics\Name\Transformer\FullNameTransformer'
        );

        $fullNameTransformer
            ->expects($this->once())
            ->method("transform")
            ->with(FullName::fromString("Ns\\foo"))
            ->willReturn(FullName::fromString("boo"))
        ;

        $transformer = new ByFullNameNameTransformer($fullNameTransformer);

        $this->assertEquals(
            FullName::fromString("boo"),
            $transformer->transformName(
                $name,
                $context
            )
        );
    }

    /**
     * @test
     */
    public function it_directly_transforms_fullnames()
    {
        $context = NamespaceContext::fromNamespaceName("Ns");
        $name = new FullName(array("foo"));

        $fullNameTransformer = $this->getMock(
            '\NicMart\Generics\Name\Transformer\FullNameTransformer'
        );

        $fullNameTransformer
            ->expects($this->once())
            ->method("transform")
            ->with(FullName::fromString("foo"))
            ->willReturn(FullName::fromString("boo"))
        ;

        $transformer = new ByFullNameNameTransformer($fullNameTransformer);

        $this->assertEquals(
            FullName::fromString("boo"),
            $transformer->transformName(
                $name,
                $context
            )
        );
    }
}
