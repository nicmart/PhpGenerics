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

use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\FullName;
use PhpParser\Node;


class FullNamePhpParserNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_uses_name_transformer()
    {
        $name = new Node\Name("B\\Class1");
        $context = new NamespaceContext(
            Namespace_::fromString("Ns"),
            array(
                Use_::fromStrings("Ns1\\Ns2", "B")
            )
        );

        $transformer = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameTransformer'
        );

        $transformer
            ->expects($this->once())
            ->method("transform")
            ->with(
                FullName::fromString("Ns1\\Ns2\\Class1")
            )
            ->willReturn(
                FullName::fromString("Ns3\\Foo")
            )
        ;

        $phpParserTransformer = new FullNamePhpParserNameTransformer($transformer);

        $this->assertEquals(
            new Node\Name\FullyQualified("Ns3\\Foo"),
            $phpParserTransformer->transform($name, $context)
        );
    }

    /**
     * @test
     */
    public function it_uses_name_transformer_with_full_qualified_names()
    {
        $name = new Node\Name\FullyQualified("B\\Class1");
        $context = new NamespaceContext(
            Namespace_::fromString("Ns"),
            array(
                Use_::fromStrings("Ns1\\Ns2", "B")
            )
        );

        $transformer = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameTransformer'
        );

        $transformer
            ->expects($this->once())
            ->method("transform")
            ->with(
                FullName::fromString("B\\Class1")
            )
            ->willReturn(
                FullName::fromString("Ns3\\Foo")
            )
        ;

        $phpParserTransformer = new FullNamePhpParserNameTransformer($transformer);

        $this->assertEquals(
            new Node\Name\FullyQualified("Ns3\\Foo"),
            $phpParserTransformer->transform($name, $context)
        );
    }
}
