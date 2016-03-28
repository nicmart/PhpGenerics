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
            new TypeAssignment(new Type("A\\B"), new Type("C"))
        ));

        $this->assertTrue($context->hasAssignmentFrom(new Type("A\\B")));
        $this->assertFalse($context->hasAssignmentFrom(new Type("A")));
    }

    public function it_transforms_types()
    {
        $context = new TypeAssignmentContext(array(
            new TypeAssignment(new Type("A\\B"), new Type("C")),
            new TypeAssignment(new Type("B\\C"), new Type("D\\E")),
        ));

        $this->assertEquals(
            new Type("C"),
            $context->transformType(new Type("A\\B"))
        );

        $this->assertEquals(
            new Type("D\\E"),
            $context->transformType(new Type("\\B\\C"))
        );

        $this->assertEquals(
            new Type("Ns\\Object"),
            $context->transformType(new Type("Ns\\Object"))
        );
    }
}
