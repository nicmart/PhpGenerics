<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\PhpDoc;

use NicMart\Generics\Adapter\PhpParserDocToPhpdoc;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use NicMart\Generics\Name\Transformer\ByFullNameNameTransformer;
use phpDocumentor\Reflection\DocBlock;
use PhpParser\Comment\Doc;

/**
 * Class ReplaceTypePhpDocTransformerTest
 * @package NicMart\Generics\Compiler\PhpDoc
 */
class ReplaceTypePhpDocTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @throws \InvalidArgumentException
     */
    public function it_replaces_types()
    {
        $assignments = NameAssignmentContext::fromStrings(array(
            'C\D\E' => 'F\G',
            'Ns1\Ns2\B' => 'F\H'
        ));

        $nsContext = new NamespaceContext(
            Namespace_::fromString("Ns1\\Ns2"),
            new Uses(array(
                Use_::fromStrings('C\D\E', 'A')
            ))
        );

        $compiler = new ReplaceTypePhpDocTransformer(
            new ByFullNameNameTransformer($assignments),
            $transformer = new PhpParserDocToPhpdoc(),
            new DocBlock\Serializer()
        );

        $phpdoc = new Doc('
            /**
             * @param A|B|string $var1 desc1
             * @param B $var2 desc2
             * @param C $var3
             * @param \C\D\E $var4
             * @return A
             */
        ');


        $expectedPhpDoc = new Doc('
            /**
             * @param \F\G|\F\H|string $var1 desc1
             * @param \F\H $var2 desc2
             * @param C $var3
             * @param \F\G $var4
             * @return \F\G
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
