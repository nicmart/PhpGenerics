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

use NicMart\Generics\Type\Assignment\TypeAssignment;
use NicMart\Generics\Type\Assignment\TypeAssignmentContext;
use NicMart\Generics\Type\Context\Namespace_;
use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\Context\Use_;
use NicMart\Generics\Type\Type;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

class TypeTransformerVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BuilderFactory
     */
    private $nodeFactory;

    public function setUp()
    {
        $this->nodeFactory = new BuilderFactory();
    }

    /**
     * @test
     */
    public function it_transforms_news()
    {
        $new = new Expr\New_(new Name("Cls"));
        $new->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            new NamespaceContext(new Namespace_("NS1\\NS2"))
        );

        $visitor = new TypeTransformerVisitor(
            new TypeAssignmentContext(array(
                new TypeAssignment(
                    new Type("NS1\\NS2\\Cls"),
                    new Type("A\\B\\C")
                )
            ))
        );

        $visitor->enterNode($new);

        $this->assertEquals(
            "A\\B\\C",
            $new->class->toString()
        );

        // With use statement
        $new->setAttribute(
            NamespaceContextVisitor::ATTR_NAME,
            NamespaceContext::emptyContext()->withUse(
                new Use_("NS1\\NS2\\Cls")
            )
        );

        $visitor->enterNode($new);
        $this->assertEquals(
            "A\\B\\C",
            $new->class->toString()
        );
    }
}
