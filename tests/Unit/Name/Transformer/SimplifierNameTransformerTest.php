<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 18/04/2016, 17:41
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Transformer;

use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

/**
 * Class SimplifierNameTransformerTest
 * @package NicMart\Generics\Name\Transformer
 */
class SimplifierNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_simplify_name()
    {
        $context = NamespaceContext::fromNamespaceName("Ns");
        $name = RelativeName::fromString("Class1");

        $nameSimplifier = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameSimplifier'
        );

        $nameSimplifier
            ->expects($this->once())
            ->method("simplify")
            ->with(FullName::fromString("Ns\\Class1"))
            ->willReturn(RelativeName::fromString("Class2"))
        ;

        $simplifierTransformer = new SimplifierNameTransformer(
            $nameSimplifier
        );

        $this->assertEquals(
            RelativeName::fromString("Class2"),
            $simplifierTransformer->transformName(
                $name,
                $context
            )
        );
    }
}
