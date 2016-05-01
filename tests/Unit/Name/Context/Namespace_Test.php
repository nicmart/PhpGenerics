<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 06/04/2016, 13:36
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Context;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

/**
 * Class Namespace_Test
 * @package NicMart\Generics\Name\Context
 */
class Namespace_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_qualifies_name()
    {
        $relative = RelativeName::fromString("C\\D");
        $ns = Namespace_::fromString("A\\B");

        $this->assertEquals(
            FullName::fromString("A\\B\\C\\D"),
            $ns->qualify($relative)
        );
    }

    /**
     * @test
     */
    public function it_does_not_mess_up_with_native_types()
    {
        $relative = RelativeName::fromString("string");
        $ns = Namespace_::fromString("A\\B");

        $this->assertEquals(
            FullName::fromString("string"),
            $ns->qualify($relative)
        );
    }

    /**
     * @test
     */
    public function it_simplifies_name()
    {
        $name = FullName::fromString("A\\B\\C\\D");
        $ns = Namespace_::fromString("A\\B");

        $this->assertEquals(
            RelativeName::fromString("C\\D"),
            $ns->simplify($name)
        );

        $this->assertEquals(
            RelativeName::fromString("C\\D«T»"),
            $ns->simplify(FullName::fromString("A\\B\\C\\D«T»"))
        );
    }
}
