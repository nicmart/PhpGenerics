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


use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use PhpParser\BuilderFactory;

class AddUsesVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_adds_uses_as_children_to_namespace_node()
    {
        $nodeFactory = new BuilderFactory();
        $visitor = new AddUsesVisitor(new Uses(array(
            Use_::fromStrings("Ns1\\AFTER"),
            Use_::fromStrings("Ns1\\Class2", "Alias"),
        )));

        $ns = $nodeFactory->namespace("NS1\\NS2")->addStmts(array(
            $nodeFactory->use("Ns1\\BEFORE")->getNode(),
            $nodeFactory->use("Ns1\\AFTER")->getNode(),
            $nodeFactory->class("boh")->getNode()
        ))->getNode();

        $visitor->enterNode($ns);

        $this->assertCount(
            4,
            $ns->stmts
        );

        $this->assertEquals(
            $nodeFactory->use("Ns1\\BEFORE")->getNode(),
            $ns->stmts[0]
        );

        $this->assertEquals(
            $nodeFactory->use("Ns1\\AFTER")->getNode(),
            $ns->stmts[1]
        );

        $this->assertEquals(
            $nodeFactory->use("Ns1\\Class2")->as("Alias")->getNode(),
            $ns->stmts[2]
        );

        $this->assertEquals(
            $nodeFactory->class("boh")->getNode(),
            $ns->stmts[3]
        );
    }
}
