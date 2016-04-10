<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Adapter;


use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use phpDocumentor\Reflection\DocBlock;
use PhpParser\Comment\Doc;

class PhpParserDocToPhpdocTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @throws \InvalidArgumentException
     */
    public function it_transforms()
    {
        $transformer = new PhpParserDocToPhpdoc();
        $doc = new Doc('
            /**
             * @param string $x Bla bla
             */
        ');

        $nsContext = new NamespaceContext(
            Namespace_::fromString("Ns1\\Ns2"),
            array(
                Use_::fromStrings("A", "B"),
                Use_::fromStrings("C\\D", "E"),
            )
        );

        $expected = new DocBlock(
            $doc->getText(),
            new DocBlock\Context(
                "Ns1\\Ns2",
                array(
                    "B" => "A",
                     "E" => "C\\D"
                )
            )
        );

        $this->assertEquals(
            $expected,
            $transformer->transform($doc, $nsContext)
        );
    }
}
