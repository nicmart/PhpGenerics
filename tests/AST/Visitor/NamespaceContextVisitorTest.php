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


use NicMart\Generics\Type\Context\Namespace_;
use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\Context\Use_;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class NamespaceContextVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BuilderFactory
     */
    private $nodeFactory;

    /**
     * @var NamespaceContextVisitor
     */
    private $visitor;

    public function setUp()
    {
        $this->nodeFactory = new BuilderFactory();
        $this->visitor = new NamespaceContextVisitor();
    }

    /**
     * @test
     */
    public function it_parses_namespaces()
    {
       $ns1 = $this->nodeFactory->namespace("A\\B\\C")->addStmts(array(
           $class = $this->nodeFactory->class("D")->getNode()
       ))->getNode();

        $ns2 = $this->nodeFactory->namespace("A\\E\\F")->addStmts(array(
            $func = $this->nodeFactory->function("foo")->getNode()
        ))->getNode();

        $this->visitor->enterNode($ns1);
        $this->assertEquals(
            NamespaceContext::emptyContext(),
            $ns1->getAttribute(NamespaceContextVisitor::ATTR_NAME)
        );

        $this->visitor->enterNode($class);
        $this->assertEquals(
            new NamespaceContext(new Namespace_("A\\B\\C")),
            $class->getAttribute(NamespaceContextVisitor::ATTR_NAME)
        );

        $this->visitor->enterNode($ns2);
        $this->assertEquals(
            NamespaceContext::emptyContext(),
            $ns1->getAttribute(NamespaceContextVisitor::ATTR_NAME)
        );

        $this->visitor->enterNode($func);
        $this->assertEquals(
            new NamespaceContext(new Namespace_("A\\E\\F")),
            $func->getAttribute(NamespaceContextVisitor::ATTR_NAME)
        );
    }

    /**
     * @test
     */
    public function it_parses_use_statements()
    {
        $use = $this->nodeFactory->use("A\\B")->as("C")->getNode();
        $new = new Expr\New_(new Name("C"));

        $this->visitor->enterNode($use);
        $this->visitor->enterNode($new);

        $this->assertEquals(
            NamespaceContext::emptyContext()
                ->withUse(
                    new Use_("A\\B", "C")
                )
            ,
            $new->getAttribute(NamespaceContextVisitor::ATTR_NAME)
        );
    }
}
