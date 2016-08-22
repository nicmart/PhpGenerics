<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor\Adapter;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use phpDocumentor\Reflection\Types\Context;

class PhpDocContextAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testToPhpDocContext()
    {
        $nsContext = NamespaceContext::fromNamespaceName("Foo\\Bar")
            ->withUse(Use_::fromStrings("A\\B", "C"))
            ->withUse(Use_::fromStrings("D\\E"))
        ;

        $phpDocContext = new Context(
            "Foo\\Bar", array(
                "C" => "A\\B",
                "E" => "D\\E",
            )
        );

        $adapter = new PhpDocContextAdapter();

        $this->assertEquals(
            $phpDocContext,
            $adapter->toPhpDocContext($nsContext)
        );
    }

    public function testFromPhpDocContext()
    {
        $nsContext = NamespaceContext::fromNamespaceName("Foo\\Bar")
            ->withUse(Use_::fromStrings("A\\B", "C"))
            ->withUse(Use_::fromStrings("D\\E"))
        ;

        $phpDocContext = new Context(
            "Foo\\Bar", array(
                "C" => "A\\B",
                "E" => "D\\E",
            )
        );

        $adapter = new PhpDocContextAdapter();

        $this->assertEquals(
            $nsContext,
            $adapter->fromPhpDocContext($phpDocContext)
        );
    }
}
