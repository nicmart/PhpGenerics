<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic;


use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Assignment\SimpleNameAssignmentContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

class AngleQuotedGenericNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_name()
    {
        $generic = new AngleQuotedGenericName(
            FullName::fromString("Ns\\Class1")
        );

        $this->assertEquals(
            FullName::fromString("Ns\\Class1"),
            $generic->name()
        );
    }

    /**
     * @test
     */
    public function it_applies()
    {
        $generic = AngleQuotedGenericName::fromString(
            "Ns\\Class1«T·S»"
        );

        $concreteName = $generic->apply(array(
            FullName::fromString("A\\B\\Class2"),
            FullName::fromString("A\\B\\C\\Class3"),
        ));

        $this->assertEquals(
            FullName::fromString("Ns\\Class1«Class2·Class3»"),
            $concreteName
        );

        $this->setExpectedException(
            get_class(new \InvalidArgumentException())
        );

        $generic->apply(array(
            FullName::fromString("A\\B\\Class2")
        ));
    }

    /**
     * @test
     */
    public function it_returns_assignments()
    {
        $generic = AngleQuotedGenericName::fromString("Ns\\Class1«T»");
        $relativeName = RelativeName::fromString("T");
        $qualifier = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameQualifier'
        );

        $qualifier
            ->expects($this->once())
            ->method("qualify")
            ->with($relativeName)
            ->willReturn(FullName::fromString("Foo"))
        ;

        $assignments = $generic->assignments(
            array(FullName::fromString("A\\B")),
            $qualifier
        );

        $this->assertEquals(
            NameAssignmentContext::fromStrings(array(
                "Foo" => "A\\B"
            )),
            $assignments
        );
    }

    /**
     * @test
     */
    public function it_returns_simple_name_assignments()
    {
        $generic = AngleQuotedGenericName::fromString("Ns\\Class1«T·S»");

        $assignments = $generic->simpleAssignments(
            array(
                FullName::fromString("A\\B"),
                FullName::fromString("C\\D")
            )
        );

        $this->assertEquals(
            SimpleNameAssignmentContext::fromStrings(array(
                "Class1«T·S»" => "Class1«B·D»"
            )),
            $assignments
        );
    }
}
