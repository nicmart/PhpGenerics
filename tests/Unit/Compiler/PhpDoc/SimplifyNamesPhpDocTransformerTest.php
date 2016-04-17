<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Compiler\PhpDoc;


use NicMart\Generics\Adapter\PhpParserDocToPhpdoc;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\Comment\Doc;

class SimplifyNamesPhpDocTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @throws \InvalidArgumentException
     */
    public function it_replaces_types()
    {
        $transformer = new PhpParserDocToPhpdoc();
        $compiler = new SimplifyNamesPhpDocTransformer(
            $transformer,
            new Serializer()
        );

        $phpdoc = new Doc('
            /**
             * @param Ns1\Ns2\Class1 $var1 desc1
             * @param \A\B\C $var2 desc2
             * @param Boh $var3
             */
        ');

        $nsContext = new NamespaceContext(
            Namespace_::fromString("Ns1\\Ns2"),
            array(
                Use_::fromStrings('A\B\C', 'D')
            )
        );

        $expectedPhpDoc = new Doc('
            /**
             * @param Class1 $var1 desc1
             * @param D $var2 desc2
             * @param Boh $var3
             */
        ');

        $compiledPhpDoc = $compiler->transform(
            $phpdoc,
            $nsContext
        );

        $this->assertDocblockEquals(
            $transformer->transform($expectedPhpDoc, $nsContext),
            $transformer->transform($compiledPhpDoc, $nsContext)
        );
    }

    private function assertDocblockEquals(
        DocBlock $docBlock1,
        DocBlock $docBlock2
    ) {
        $serializer = new DocBlock\Serializer();

        $this->assertEquals(
            $serializer->getDocComment($docBlock1),
            $serializer->getDocComment($docBlock2)
        );
    }
}
