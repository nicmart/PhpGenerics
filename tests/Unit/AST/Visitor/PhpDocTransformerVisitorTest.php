<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor;


use NicMart\Generics\Adapter\PhpParserDocToPhpdoc;
use NicMart\Generics\Compiler\PhpDoc\PhpDocTransformer;
use NicMart\Generics\Compiler\PhpDoc\ReplaceTypePhpDocTransformer;
use NicMart\Generics\Name\Assignment\NameAssignment;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;

/**
 * Class PhpDocTransformerVisitorTest
 * @package NicMart\Generics\AST\Visitor
 */
class PhpDocTransformerVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_transforms_phpdocs()
    {
        $nodeFactory = new BuilderFactory();

        $phpdoc = new Doc('
            /**
             * @param A $x bla bla
             * @return B bla
             */
        ');

        $method = $nodeFactory
            ->method("foo")
            ->setDocComment($phpdoc)
            ->getNode()
        ;

        $method->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            $nsContext = new NamespaceContext(
                Namespace_::fromString("Ns1\\Ns2")
            )
        );

        /** @var PhpDocTransformer $phpdocTransformer */
        $phpdocTransformer = $this->getMock(
            '\NicMart\Generics\Compiler\PhpDoc\PhpDocTransformer'
        );

        $phpdocTransformer
            ->expects($this->once())
            ->method("transform")
            ->with(
                $phpdoc,
                $nsContext
            )
            ->willReturn(new Doc("foo"))
        ;

        $visitor = new PhpDocTransformerVisitor(
            $phpdocTransformer
        );

        $visitor->enterNode($method);

        $this->assertEquals(
            new Doc("foo"),
            $method->getDocComment()
        );
    }
}
