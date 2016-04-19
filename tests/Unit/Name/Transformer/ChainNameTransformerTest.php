<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

class ChainNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_uses_transformers()
    {
        $context = NamespaceContext::fromNamespaceName("foo");
        $transformer1 = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameTransformer'
        );
        $transformer2 = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameTransformer'
        );

        $name = RelativeName::fromString("a");

        $transformer1
            ->expects($this->once())
            ->method("transformName")
            ->with(
                $name,
                $context
            )
            ->willReturn(
                FullName::fromString("b")
            )
        ;
        $transformer2
            ->expects($this->once())
            ->method("transformName")
            ->with(
                FullName::fromString("b"),
                $context
            )
            ->willReturn(
                RelativeName::fromString("c")
            )
        ;

        $chain = new ChainNameTransformer(array(
            $transformer1,
            $transformer2
        ));

        $this->assertEquals(
            RelativeName::fromString("c"),
            $chain->transformName($name, $context)
        );
    }
}
