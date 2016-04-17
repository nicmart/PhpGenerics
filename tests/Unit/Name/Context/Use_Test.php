<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 06/04/2016, 12:43
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Context;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\SimpleName;

class Use_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_qualifies_relative_names()
    {
        $use = new Use_(
            FullName::fromString("A\\B\\C"),
            new SimpleName("Alias")
        );

        $relativeName = RelativeName::fromString("Alias\\D");

        $this->assertEquals(
            FullName::fromString("A\\B\\C\\D"),
            $use->qualify($relativeName)
        );

        $relativeName = RelativeName::fromString("Alias");

        $this->assertEquals(
            FullName::fromString("A\\B\\C"),
            $use->qualify($relativeName)
        );

        $relativeName = RelativeName::fromString("A\\C");

        $this->assertEquals(
            FullName::fromString("A\\C"),
            $use->qualify($relativeName)
        );
    }

    /**
     * @test
     */
    public function it_simplifies_full_names()
    {
        $use = new Use_(
            FullName::fromString("A\\B\\C"),
            new SimpleName("Alias")
        );

        $fullName = FullName::fromString("A\\B\\C\\D");

        $this->assertEquals(
            RelativeName::fromString("Alias\\D"),
            $use->simplify($fullName)
        );

        $fullName = FullName::fromString("A\\B\\C");

        $this->assertEquals(
            RelativeName::fromString("Alias"),
            $use->simplify($fullName)
        );

        $fullName = FullName::fromString("B\\A\\C");

        $this->assertEquals(
            RelativeName::fromString("B\\A\\C"),
            $use->simplify($fullName)
        );
    }
}
