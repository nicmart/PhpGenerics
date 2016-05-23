<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor;

use NicMart\Generics\Generic;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use PhpParser\BuilderFactory;

class RemoveParentTypeVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_removes_parent_classes()
    {
        $nameToRemove = '\NicMart\Generics\Generic';
        $nodeFactory = new BuilderFactory();

        $visitor = new RemoveParentTypeVisitor(array(
            FullName::fromString($nameToRemove)
        ));

        $class = $nodeFactory
            ->class("Boo")
            ->extend($nameToRemove)
            ->getNode()
        ;
        $class->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::emptyContext()
        );

        $visitor->enterNode($class);
        $visitor->leaveNode($class);

        $this->assertNull(
            $class->extends
        );
    }

    /**
     * @test
     */
    public function it_removes_parent_interfaces()
    {
        $nameToRemove = '\NicMart\Generics\Generic';
        $nodeFactory = new BuilderFactory();

        $visitor = new RemoveParentTypeVisitor(array(
            FullName::fromString($nameToRemove)
        ));

        $interface = $nodeFactory
            ->interface("Boo")
            ->extend($nameToRemove)
            ->getNode()
        ;
        $interface->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::emptyContext()
        );

        $visitor->enterNode($interface);
        $visitor->leaveNode($interface);

        $this->assertEquals(
            array(),
            $interface->extends
        );
    }

    /**
     * @test
     */
    public function it_removes_interfaces_from_classes()
    {
        $nameToRemove = '\NicMart\Generics\Generic';
        $nodeFactory = new BuilderFactory();

        $visitor = new RemoveParentTypeVisitor(array(
            FullName::fromString($nameToRemove)
        ));

        $class = $nodeFactory
            ->class("Boo")
            ->implement($nameToRemove)
            ->getNode()
        ;
        $class->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::emptyContext()
        );

        $visitor->enterNode($class);
        $visitor->leaveNode($class);

        $this->assertEquals(
            array(),
            $class->implements
        );
    }
}
