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
class NameAssignmentContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_checks_if_assignment_exists()
    {
        $context = new NameAssignmentContext(array(

            new NameAssignment(FullName::fromString("A\\B"), FullName::fromString("C"))
        ));

        $this->assertTrue($context->hasAssignmentFrom(FullName::fromString("A\\B")));
        $this->assertFalse($context->hasAssignmentFrom(FullName::fromString("A")));
    }

    public function it_transforms_types()
    {
        $context = new NameAssignmentContext(array(
            new NameAssignment(FullName::fromString("A\\B"), FullName::fromString("C")),
            new NameAssignment(FullName::fromString("B\\C"), FullName::fromString("D\\E")),
        ));

        $this->assertEquals(
            FullName::fromString("C"),
            $context->transform(FullName::fromString("A\\B"))
        );

        $this->assertEquals(
            FullName::fromString("D\\E"),
            $context->transform(FullName::fromString("\\B\\C"))
        );

        $this->assertEquals(
            FullName::fromString("Ns\\Object"),
            $context->transform(FullName::fromString("Ns\\Object"))
        );
    }

    /**
     * @test
     */
    public function it_constructs_from_simple_strings()
    {
        $context = NameAssignmentContext::fromStrings(array(
            "A\\B" => "C",
            "D" => "E\\F"
        ));

        $expected = new NameAssignmentContext(array(
            new NameAssignment(
                FullName::fromString("A\\B"),
                FullName::fromString("C")
            ),
            new NameAssignment(
                FullName::fromString("D"),
                FullName::fromString("E\\F")
            ),
        ));

        $this->assertEquals($expected, $context);
    }
}
