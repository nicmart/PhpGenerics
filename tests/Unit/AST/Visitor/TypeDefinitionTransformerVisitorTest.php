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


use NicMart\Generics\Name\Assignment\NameAssignment;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\SimpleName;
use NicMart\Generics\Name\Transformer\SimpleNameTransformer;
use PhpParser\BuilderFactory;

class TypeDefinitionTransformerVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleNameTransformer
     */
    private $simpleNameTransformer;

    public function setUp()
    {
        $this->simpleNameTransformer = $this->getMock(
            '\NicMart\Generics\Name\Transformer\SimpleNameTransformer'
        );

        $this->simpleNameTransformer
            ->expects($this->once())
            ->method("transform")
            ->with(new SimpleName("foo"))
            ->willReturn(new SimpleName("bar"))
        ;
    }

    /**
     * @test
     */
    public function it_transforms_classes()
    {
        $nodeFactory = new BuilderFactory();
        $visitor = new TypeDefinitionTransformerVisitor(
            $this->simpleNameTransformer
        );

        $class = $nodeFactory->class("foo")->getNode();
        $class->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::fromNamespaceName("NS1\\NS2")
        );

        $visitor->enterNode($class);

        $this->assertEquals(
            "bar",
            $class->name
        );
    }

    /**
     * @test
     */
    public function it_transforms_interfaces()
    {
        $nodeFactory = new BuilderFactory();
        $visitor = new TypeDefinitionTransformerVisitor(
            $this->simpleNameTransformer
        );

        $class = $nodeFactory->interface("foo")->getNode();
        $class->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::fromNamespaceName("NS1\\NS2")
        );

        $visitor->enterNode($class);

        $this->assertEquals(
            "bar",
            $class->name
        );
    }
}
