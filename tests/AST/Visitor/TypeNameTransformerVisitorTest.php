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


use NicMart\Generics\Name\Assignment\TypeAssignment;
use NicMart\Generics\Name\Assignment\TypeAssignmentContext;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use PhpParser\BuilderFactory;

class TypeNameTransformerVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeAssignmentContext
     */
    private $typeAssignments;

    public function setUp()
    {
        $this->typeAssignments = new TypeAssignmentContext(array(
            new TypeAssignment(
                FullName::fromString("NS1\\NS2\\Class1"),
                FullName::fromString("A\\Class2")
            ),
            new TypeAssignment(
                FullName::fromString("NS3\\Class3"),
                FullName::fromString("B\\C\\Class4")
            ),
        ));
    }

    /**
     * @test
     */
    public function it_transforms_namespace()
    {
        $nodeFactory = new BuilderFactory();
        $visitor = new TypeNameTransformerVisitor($this->typeAssignments);

        $ns = $nodeFactory->namespace("NS1\\NS2")->getNode();

        $visitor->enterNode($ns);

        $this->assertEquals(
            "A",
            $ns->name->toString()
        );

        $ns = $nodeFactory->namespace("NS3")->getNode();
        $visitor->enterNode($ns);

        $this->assertEquals(
            "B\\C",
            $ns->name->toString()
        );
    }

    /**
     * @test
     */
    public function it_transforms_classes()
    {
        $nodeFactory = new BuilderFactory();
        $visitor = new TypeNameTransformerVisitor($this->typeAssignments);

        $class = $nodeFactory->class("Class1")->getNode();
        $class->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::fromNamespaceName("NS1\\NS2")
        );

        $visitor->enterNode($class);

        $this->assertEquals(
            "Class2",
            $class->name
        );


        $class = $nodeFactory->class("Class3")->getNode();
        $class->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::fromNamespaceName("NS3")
        );

        $visitor->enterNode($class);

        $this->assertEquals(
            "Class4",
            $class->name
        );
    }

    /**
     * @test
     */
    public function it_transforms_interfaces()
    {
        $nodeFactory = new BuilderFactory();
        $visitor = new TypeNameTransformerVisitor($this->typeAssignments);

        $class = $nodeFactory->interface("Class1")->getNode();
        $class->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::fromNamespaceName("NS1\\NS2")
        );

        $visitor->enterNode($class);

        $this->assertEquals(
            "Class2",
            $class->name
        );


        $class = $nodeFactory->interface("Class3")->getNode();
        $class->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::fromNamespaceName("NS3")
        );

        $visitor->enterNode($class);

        $this->assertEquals(
            "Class4",
            $class->name
        );
    }
}
