<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser;

use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use PhpParser\Node;

/**
 * Class PhpNameTransformerTest
 * @package NicMart\Generics\Infrastructure\PhpParser
 */
class PhpNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_transforms_php_names()
    {
        $context = NamespaceContext::emptyContext();
        $fromRelative = new Node\Name\Relative(array("Hello"));
        $fromName = RelativeName::fromString("Hello");
        $toName = FullName::fromString("World");
        $to = new Node\Name\FullyQualified(array("World"));

        $adapter = new PhpNameAdapter();

        $nameTransformer = $this->getMock('\NicMart\Generics\Name\Transformer\NameTransformer');
        $nameTransformer
            ->expects($this->once())
            ->method("transformName")
            ->with($fromName, $context)
            ->willReturn($toName)
        ;

        $transformer = new PhpNameTransformer($adapter, $nameTransformer);

        $this->assertEquals(
            $to,
            $transformer->transform($fromRelative, $context)
        );
    }
}
