<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Namespace_\Assignment;


use NicMart\Generics\Type\Assignment\NamespaceAssignment;
use NicMart\Generics\Type\Assignment\NamespaceAssignmentContext;
use NicMart\Generics\Type\Context\Namespace_;

class NamespaceAssignmentContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_checks_if_assignment_exists()
    {
        $context = new NamespaceAssignmentContext(array(
            new NamespaceAssignment(Namespace_::fromString("A\\B"), Namespace_::fromString("C")),
        ));

        $this->assertTrue($context->hasAssignmentFrom(Namespace_::fromString("A\\B")));
        $this->assertFalse($context->hasAssignmentFrom(Namespace_::fromString("A")));
    }

    /**
     * @test
     */
    public function it_transforms_namespaces()
    {
        $context = new NamespaceAssignmentContext(array(
            new NamespaceAssignment(Namespace_::fromString("A\\B"), Namespace_::fromString("C")),
            new NamespaceAssignment(Namespace_::fromString("B\\C"), Namespace_::fromString("D\\E")),
        ));

        $this->assertEquals(
            Namespace_::fromString("C"),
            $context->transformNamespace(Namespace_::fromString("A\\B"))
        );

        $this->assertEquals(
            Namespace_::fromString("D\\E"),
            $context->transformNamespace(Namespace_::fromString("\\B\\C"))
        );

        $this->assertEquals(
            Namespace_::fromString("Ns\\Nss"),
            $context->transformNamespace(Namespace_::fromString("Ns\\Nss"))
        );
    }
}
