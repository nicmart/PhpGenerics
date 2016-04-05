<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Assignment;

use NicMart\Generics\Name\FullName;

/**
 * Class TypeAssignmentContextTest
 * @package NicMart\Generics\Name\Assignment
 */
class TypeAssignmentContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_checks_if_assignment_exists()
    {
        $context = new TypeAssignmentContext(array(

            new TypeAssignment(FullName::fromString("A\\B"), FullName::fromString("C"))
        ));

        $this->assertTrue($context->hasAssignmentFrom(FullName::fromString("A\\B")));
        $this->assertFalse($context->hasAssignmentFrom(FullName::fromString("A")));
    }

    public function it_transforms_types()
    {
        $context = new TypeAssignmentContext(array(
            new TypeAssignment(FullName::fromString("A\\B"), FullName::fromString("C")),
            new TypeAssignment(FullName::fromString("B\\C"), FullName::fromString("D\\E")),
        ));

        $this->assertEquals(
            FullName::fromString("C"),
            $context->transformType(FullName::fromString("A\\B"))
        );

        $this->assertEquals(
            FullName::fromString("D\\E"),
            $context->transformType(FullName::fromString("\\B\\C"))
        );

        $this->assertEquals(
            FullName::fromString("Ns\\Object"),
            $context->transformType(FullName::fromString("Ns\\Object"))
        );
    }
}
