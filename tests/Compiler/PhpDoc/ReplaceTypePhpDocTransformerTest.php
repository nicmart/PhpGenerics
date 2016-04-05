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

use NicMart\Generics\Type\Assignment\TypeAssignment;
use NicMart\Generics\Type\Assignment\TypeAssignmentContext;
use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\Type;
use phpDocumentor\Reflection\DocBlock;

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
        $compiler = new ReplaceTypePhpDocTransformer();

        $phpdoc = '
            /**
             * @param A|B|string $var1 desc1
             * @param B $var2 desc2
             * @param C $var3
             * @return A
             */
        ';

        $docBlock = new DocBlock(
            $phpdoc,
            $context = new DocBlock\Context(
                "Ns1\\Ns2",
                 array(
                     'A' => 'C\D\E',
                 )
            )
        );

        $assignments = array(
            'C\D\E' => 'F\G',
            'Ns1\Ns2\B' => 'F\H'
        );

        $assignments = new TypeAssignmentContext(array(
            new TypeAssignment(
                Type::fromString('C\D\E'),
                Type::fromString('F\G')
            ),
            new TypeAssignment(
                Type::fromString('Ns1\Ns2\B'),
                Type::fromString('F\H')
            )
        ));

        $expectedPhpDoc = '
            /**
             * @param F\G|F\H|string $var1 desc1
             * @param F\H $var2 desc2
             * @param C $var3
             * @return F\G
             */
        ';

        $expectedPhpDoc = new DocBlock(
            $expectedPhpDoc,
            $context
        );

        $compiledPhpDoc = $compiler->transform(
            $docBlock,
            NamespaceContext::emptyContext(),
            $assignments
        );

        $this->assertDocblockEquals(
            $expectedPhpDoc,
            $compiledPhpDoc
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
