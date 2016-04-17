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
use NicMart\Generics\Name\RelativeName;
use PhpParser\Node;

class NameSimplifierPhpParserNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_uses_name_simplifier()
    {
        $name = new Node\Name("B\\Class1");
        $context = new NamespaceContext(
            Namespace_::fromString("Ns"),
            array(
                Use_::fromStrings("Ns1\\Ns2", "B")
            )
        );

        $simplifier = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameSimplifier'
        );

        $simplifier
            ->expects($this->once())
            ->method("simplify")
            ->with(
                FullName::fromString("Ns1\\Ns2\\Class1")
            )
            ->willReturn(
                RelativeName::fromString("Ns3\\Foo")
            )
        ;

        $phpParserTransformer = new NameSimplifierPhpParserNameTransformer(
            $simplifier
        );

        $this->assertEquals(
            new Node\Name("Ns3\\Foo"),
            $phpParserTransformer->transform($name, $context)
        );
    }

    /**
     * @test
     */
    public function it_uses_name_simplifier_with_full_qualified_names()
    {
        $name = new Node\Name\FullyQualified("B\\Class1");
        $context = new NamespaceContext(
            Namespace_::fromString("Ns")
        );

        $simplifier = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameSimplifier'
        );

        $simplifier
            ->expects($this->once())
            ->method("simplify")
            ->with(
                FullName::fromString("B\\Class1")
            )
            ->willReturn(
                RelativeName::fromString("Ns3\\Foo")
            )
        ;

        $phpParserTransformer = new NameSimplifierPhpParserNameTransformer(
            $simplifier
        );

        $this->assertEquals(
            new Node\Name("Ns3\\Foo"),
            $phpParserTransformer->transform($name, $context)
        );
    }
}
