<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser\Name;


use PhpParser\Node\Name;
use PhpParser\Node\Stmt\UseUse;

class UseUseNameManipulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NameManipulator
     */
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new UseUseNameManipulator();
    }

    public function testRead()
    {
        $name = new Name("Foo\\Bar\\Baz");

        $useUse = new UseUse(
            $name,
            "Blah"
        );


        $this->assertEquals(
            new Name\FullyQualified($name->parts),
            $this->manipulator->readName($useUse)
        );
    }

    public function testWrite()
    {
        $name = new Name("Foo\\Bar\\Baz");

        $useUse = new UseUse(
            $name,
            "Blah"
        );

        $transformed = $this->manipulator->withName(
            $useUse,
            new Name("A\\B\\C")
        );

        $this->assertEquals(
            new Name("A\\B\\C"),
            $transformed->name
        );

        $this->assertEquals(
            "Blah",
            $transformed->alias
        );
    }
}
