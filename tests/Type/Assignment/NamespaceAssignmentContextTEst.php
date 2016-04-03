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
            new NamespaceAssignment(new Namespace_("A\\B"), new Namespace_("C"))
        ));

        $this->assertTrue($context->hasAssignmentFrom(new Namespace_("A\\B")));
        $this->assertFalse($context->hasAssignmentFrom(new Namespace_("A")));
    }

    public function it_transforms_namespaces()
    {
        $context = new NamespaceAssignmentContext(array(
            new NamespaceAssignment(new Namespace_("A\\B"), new Namespace_("C")),
            new NamespaceAssignment(new Namespace_("B\\C"), new Namespace_("D\\E")),
        ));

        $this->assertEquals(
            new Namespace_("C"),
            $context->transformNamespace(new Namespace_("A\\B"))
        );

        $this->assertEquals(
            new Namespace_("D\\E"),
            $context->transformNamespace(new Namespace_("\\B\\C"))
        );

        $this->assertEquals(
            new Namespace_("Ns\\Nss"),
            $context->transformNamespace(new Namespace_("Ns\\Nss"))
        );
    }
}
