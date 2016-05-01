<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Context;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

class UsesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_simplifies()
    {
        $uses = new Uses(array(
            Use_::fromStrings("A\\B\\C", "D"),
            Use_::fromStrings("A\\F\\G", "E"),
        ));

        $this->assertEquals(
            $uses->simplify(FullName::fromString("A\\B\\C\\H\\I")),
            RelativeName::fromString("D\\H\\I")
        );

        $this->assertEquals(
            $uses->simplify(FullName::fromString("A\\F\\G")),
            RelativeName::fromString("E")
        );
    }
}
