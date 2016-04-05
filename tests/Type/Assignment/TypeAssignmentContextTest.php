<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Assignment;


use NicMart\Generics\Type\Type;

class TypeAssignmentContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_checks_if_assignment_exists()
    {
        $context = new TypeAssignmentContext(array(

            new TypeAssignment(Type::fromString("A\\B"), Type::fromString("C"))
        ));

        $this->assertTrue($context->hasAssignmentFrom(Type::fromString("A\\B")));
        $this->assertFalse($context->hasAssignmentFrom(Type::fromString("A")));
    }

    public function it_transforms_types()
    {
        $context = new TypeAssignmentContext(array(
            new TypeAssignment(Type::fromString("A\\B"), Type::fromString("C")),
            new TypeAssignment(Type::fromString("B\\C"), Type::fromString("D\\E")),
        ));

        $this->assertEquals(
            Type::fromString("C"),
            $context->transformType(Type::fromString("A\\B"))
        );

        $this->assertEquals(
            Type::fromString("D\\E"),
            $context->transformType(Type::fromString("\\B\\C"))
        );

        $this->assertEquals(
            Type::fromString("Ns\\Object"),
            $context->transformType(Type::fromString("Ns\\Object"))
        );
    }
}
