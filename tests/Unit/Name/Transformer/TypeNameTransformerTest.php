<?php
/**
 * This file is part of PhpStorm
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
use NicMart\Generics\Type\PrimitiveType;

/**
 * Class TypeNameTransformerTest
 * @package NicMart\Generics\Name\Transformer
 */
class TypeNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_uses_inner_transformer()
    {
        $context = NamespaceContext::emptyContext();
        $fromRelative = RelativeName::fromString("Hello");
        $from = FullName::fromString("Hello");
        $fromType = PrimitiveType::fromString("string");
        $toType = PrimitiveType::fromString("int");
        $to = FullName::fromString("World");

        $parser = $this->getMock('\NicMart\Generics\Type\Parser\TypeParser');
        $parser
            ->expects($this->once())
            ->method("parse")
            ->with($from)
            ->willReturn($fromType)
        ;

        $transformer = $this->getMock('\NicMart\Generics\Type\Transformer\TypeTransformer');
        $transformer
            ->expects($this->once())
            ->method("transform")
            ->with($fromType)
            ->willReturn($toType)
        ;

        $serializer = $this->getMock('\NicMart\Generics\Type\Serializer\TypeSerializer');
        $serializer
            ->expects($this->once())
            ->method("serialize")
            ->with($toType)
            ->willReturn($to)
        ;

        $nameTransformer = new TypeNameTransformer(
            $parser,
            $transformer,
            $serializer
        );

        $this->assertEquals(
            $to,
            $nameTransformer->transformName($fromRelative, $context)
        );
    }
}
