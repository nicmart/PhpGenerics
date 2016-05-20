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
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Name\RelativeName;

/**
 * Class GenericNameTransformerTest
 * @package NicMart\Generics\Name\Transformer
 */
class GenericNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_transforms_generic_params()
    {
        $name = FullName::fromString("Boh");
        $context = NamespaceContext::fromNamespaceName("A\\B");

        $generic = new GenericName(
            FullName::fromString("A"),
            array(
                FullName::fromString("T1"),
                FullName::fromString("T2"),
            )
        );

        $transformedGenerics = new GenericName(
            FullName::fromString("A"),
            array(
                FullName::fromString("A"),
                FullName::fromString("B"),
            )
        );

        $innerTransformer = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameTransformer'
        );
        $innerTransformer
            ->expects($this->exactly(2))
            ->method("transformName")
            ->withConsecutive(
                array(
                    FullName::fromString("T1")
                ),
                array(
                    FullName::fromString("T2")
                )
            )
            ->willReturnOnConsecutiveCalls(
                FullName::fromString("A"),
                FullName::fromString("B")
            )
        ;

        $genericFactory = $this->getMock(
            '\NicMart\Generics\Name\Generic\Factory\GenericNameFactory'
        );

        $genericFactory
            ->expects($this->once())
            ->method("isGeneric")
            ->with($name)
            ->willReturn(true)
        ;

        $genericFactory
            ->expects($this->once())
            ->method("toGeneric")
            ->with($name, $context)
            ->willReturn($generic)
        ;

        $genericFactory
            ->expects($this->once())
            ->method("fromGeneric")
            ->with($transformedGenerics)
            ->willReturn(FullName::fromString("Foo"))
        ;

        $transformer = new GenericNameTransformer(
            $innerTransformer,
            $genericFactory
        );

        $this->assertEquals(
            $transformer->transformName(
                $name,
                $context
            ),
            FullName::fromString("Foo")
        );
    }
}
