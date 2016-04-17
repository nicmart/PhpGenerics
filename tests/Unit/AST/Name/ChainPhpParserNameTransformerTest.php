<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Name;


use NicMart\Generics\Name\Context\NamespaceContext;
use PhpParser\Node\Name;

class ChainPhpParserNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_uses_transformers()
    {
        $context = NamespaceContext::fromNamespaceName("foo");
        $transformer1 = $this->getMock(
            '\NicMart\Generics\AST\Name\PhpParserNameTransformer'
        );
        $transformer2 = $this->getMock(
            '\NicMart\Generics\AST\Name\PhpParserNameTransformer'
        );

        $name = new Name("a");

        $transformer1
            ->method("transform")
            ->with(
                $name,
                $context
            )
            ->willReturn(
                new Name("b")
            )
        ;
        $transformer2
            ->method("transform")
            ->with(
                new Name("b"),
                $context
            )
            ->willReturn(
                new Name("c")
            )
        ;

        $chain = new ChainPhpParserNameTransformer(array(
            $transformer1,
            $transformer2
        ));

        $this->assertEquals(
            new Name("c"),
            $chain->transform($name, $context)
        );
    }
}
