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
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\FullName;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\Node\Name;

class TypeTransformerVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataClass
     * @test
     * @param Node $node
     * @param NamespaceContext[] $nsContexts
     * @param TypeAssignmentContext $typeAssignmentContext
     * @param $expectedClass
     * @param null $msg
     */
    public function it_transforms_classes(
        Node $node,
        array $nsContexts,
        TypeAssignmentContext $typeAssignmentContext,
        $expectedClass,
        $msg = null
    ) {
        $visitor = new TypeTransformerVisitor(
            $typeAssignmentContext
        );

        foreach ($nsContexts as $nsContext) {
            $node->setAttribute(
                NamespaceContextVisitor::ATTR_NAME,
                $nsContext
            );

            $visitor->enterNode($node);
            $this->assertEquals(
                $expectedClass,
                $node->class->toString(),
                $msg
            );
        }
    }

    /**
     * @dataProvider dataSignatures
     * @test
     * @param Node $node
     * @param NamespaceContext[] $nsContexts
     * @param TypeAssignmentContext $typeAssignmentContext
     * @param string[] $expectedParamsClasses
     * @param string $expectedReturnClass
     * @param null $msg
     */
    public function it_transforms_signatures(
        Node $node,
        array $nsContexts,
        TypeAssignmentContext $typeAssignmentContext,
        array $expectedParamsClasses,
        $expectedReturnClass = "",
        $msg = null
    ) {
        $visitor = new TypeTransformerVisitor(
            $typeAssignmentContext
        );

        foreach ($nsContexts as $nsContext) {
            $node->setAttribute(
                NamespaceContextVisitor::ATTR_NAME,
                $nsContext
            );

            $visitor->enterNode($node);

            foreach ($node->params as $i => $param) {
                $this->assertEquals(
                    $expectedParamsClasses[$i],
                    $param->type->toString(),
                    $msg
                );
            }

            if ($node->returnType) {
                $this->assertEquals(
                    $expectedReturnClass,
                    $node->returnType->toString()
                );
            }
        }
    }

    /**
     * @dataProvider dataClassExtensions
     * @test
     * @param Stmt\Class_ $node
     * @param NamespaceContext[] $nsContexts
     * @param TypeAssignmentContext $typeAssignmentContext
     * @param string $expectedExtends
     * @param null $msg
     */
    public function it_transforms_class_extends(
        Stmt\Class_ $node,
        array $nsContexts,
        TypeAssignmentContext $typeAssignmentContext,
        $expectedExtends,
        $msg = null
    ) {
        $visitor = new TypeTransformerVisitor(
            $typeAssignmentContext
        );

        foreach ($nsContexts as $nsContext) {
            $node->setAttribute(
                NamespaceContextVisitor::ATTR_NAME,
                $nsContext
            );

            $visitor->enterNode($node);

            $this->assertEquals(
                $expectedExtends,
                $node->extends->toString(),
                $msg
            );
        }
    }

    /**
     * @dataProvider dataClassImplements
     * @test
     * @param Node $node
     * @param NamespaceContext[] $nsContexts
     * @param TypeAssignmentContext $typeAssignmentContext
     * @param string[] $expectedInterfaces
     * @param null $msg
     */
    public function it_transforms_interface_lists(
        Node $node,
        array $nsContexts,
        TypeAssignmentContext $typeAssignmentContext,
        array $expectedInterfaces,
        $msg = null
    ) {
        $visitor = new TypeTransformerVisitor(
            $typeAssignmentContext
        );

        foreach ($nsContexts as $nsContext) {
            $node->setAttribute(
                NamespaceContextVisitor::ATTR_NAME,
                $nsContext
            );

            $visitor->enterNode($node);

            $actualInterfaces = $node instanceof Stmt\Class_
                ? $node->implements
                : $node->extends
            ;

            foreach ($actualInterfaces as $i => $actualInterface) {
                $this->assertEquals(
                    $expectedInterfaces[$i],
                    $actualInterface->toString(),
                    $msg
                );
            }
        }
    }

    public function dataClass()
    {
        $contextNs = new NamespaceContext(Namespace_::fromString("NS1\\NS2"));
        $contextUse = NamespaceContext::emptyContext()->withUse(
            Use_::fromStrings("NS1\\NS2\\Cls")
        );

        $assignmentContext = new TypeAssignmentContext(array(
            new TypeAssignment(
                FullName::fromString("NS1\\NS2\\Cls"),
                FullName::fromString("A\\B\\C")
            )
        ));

        return array(
            array(
                new Expr\New_(new Name("Cls")),
                array($contextNs, $contextUse),
                $assignmentContext,
                "A\\B\\C",
                "New expressions transformation"
            ),
            array(
                new Expr\Instanceof_(
                    new Expr\Variable("a"),
                    new Name("Cls")
                ),
                array($contextNs, $contextUse),
                $assignmentContext,
                "A\\B\\C",
                "Instanceof transformation"
            ),
            array(
                new Expr\StaticCall(new Name("Cls"), new Name("method")),
                array($contextNs, $contextUse),
                $assignmentContext,
                "A\\B\\C",
                "Static call transformation"
            ),
            array(
                new Expr\StaticPropertyFetch(new Name("Cls"), new Name("property")),
                array($contextNs, $contextUse),
                $assignmentContext,
                "A\\B\\C",
                "Static property transformation"
            ),
            array(
                new Expr\ClassConstFetch(new Name("Cls"), new Name("constant")),
                array($contextNs, $contextUse),
                $assignmentContext,
                "A\\B\\C",
                "Class constant transformation"
            ),

            array(
                new Expr\New_(new Name("static")),
                array($contextNs, $contextUse),
                $assignmentContext,
                "static",
                "New expressions transformation - static"
            ),
        );
    }

    public function dataSignatures()
    {
        $nodeFactory = new BuilderFactory();
        $contextNs = new NamespaceContext(Namespace_::fromString("NS1\\NS2"));
        $contextUse = NamespaceContext::emptyContext()->withUse(
            Use_::fromStrings("NS1\\NS2\\Cls")
        );

        $assignmentContext = new TypeAssignmentContext(array(
            new TypeAssignment(
                FullName::fromString("NS1\\NS2\\Cls"),
                FullName::fromString("A\\B\\C")
            )
        ));

        return array(
            array(
                $nodeFactory->function("func")
                    ->addParam($nodeFactory->param("a")->setTypeHint("Cls"))
                    ->addParam($nodeFactory->param("b")->setTypeHint("string"))
                    ->getNode()
                ,
                array($contextNs, $contextUse),
                $assignmentContext,
                array("A\\B\\C", "string"),
                null,
                "Function statement transformation"
            ),

            array(
                $nodeFactory->method("method")
                    ->addParam($nodeFactory->param("a")->setTypeHint("Cls"))
                    ->addParam($nodeFactory->param("b")->setTypeHint("string"))
                    ->getNode()
            ,
                array($contextNs, $contextUse),
                $assignmentContext,
                array("A\\B\\C", "string"),
                null,
                "Method statement transformation"
            ),

            array(
                new Expr\Closure(array(
                    "params" => array(
                        $nodeFactory->param("a")->setTypeHint("Cls")->getNode(),
                        $nodeFactory->param("b")->setTypeHint("string")->getNode()
                    )
                )),
                array($contextNs, $contextUse),
                $assignmentContext,
                array("A\\B\\C", "string"),
                null,
                "Closure expression transformation"
            )
        );
    }

    public function dataClassExtensions()
    {
        $nodeFactory = new BuilderFactory();
        $contextNs = new NamespaceContext(Namespace_::fromString("NS1\\NS2"));
        $contextUse = NamespaceContext::emptyContext()->withUse(
            Use_::fromStrings("NS1\\NS2\\Cls")
        );

        $assignmentContext = new TypeAssignmentContext(array(
            new TypeAssignment(
                FullName::fromString("NS1\\NS2\\Cls"),
                FullName::fromString("A\\B\\C")
            )
        ));

        return array(
            array(
                $nodeFactory->class("D")
                    ->extend("Cls")
                    ->getNode()
                ,
                array($contextNs, $contextUse),
                $assignmentContext,
                "A\\B\\C",
                "Class extends statement transformation"
            )
        );
    }

    public function dataClassImplements()
    {
        $nodeFactory = new BuilderFactory();
        $contextNs = new NamespaceContext(Namespace_::fromString("NS1\\NS2"));
        $contextUse = NamespaceContext::emptyContext()->withUse(
            Use_::fromStrings("NS1\\NS2\\Int1"),
            Use_::fromStrings("NS1\\NS2\\Int2")
        );

        $assignmentContext = new TypeAssignmentContext(array(
            new TypeAssignment(
                FullName::fromString("NS1\\NS2\\Int1"),
                FullName::fromString("A\\B\\C1")
            ),
            new TypeAssignment(
                FullName::fromString("NS1\\NS2\\Int2"),
                FullName::fromString("A\\B\\C2")
            ),
        ));

        return array(
            array(
                $nodeFactory->class("D")
                    ->implement("Int1")
                    ->implement("Int2")
                    ->getNode()
                ,
                array($contextNs, $contextUse),
                $assignmentContext,
                array(
                    "A\\B\\C1",
                    "A\\B\\C2"
                ),
                "Class Implements transformation"
            ),
            array(
                $nodeFactory->interface("D")
                    ->extend("Int1")
                    ->extend("Int2")
                    ->getNode()
            ,
                array($contextNs, $contextUse),
                $assignmentContext,
                array(
                    "A\\B\\C1",
                    "A\\B\\C2"
                ),
                "Interface extends transformation"
            ),
        );
    }
}
