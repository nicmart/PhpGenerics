<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 05/04/2016, 14:43
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name;

/**
 * Class PathTest
 * @package NicMart\Generics\Name
 */
class PathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_sets_parts()
    {
        $path = new Path($parts = array("a", "b", "c"));

        $this->assertEquals($parts, $path->parts());
    }

    /**
     * @test
     */
    public function it_gets_length()
    {
        $path = new Path($parts = array("a", "b", "c"));

        $this->assertEquals(
            count($parts),
            $path->length()
        );
    }

    /**
     * @test
     */
    public function it_gets_name()
    {
        $path = new Path($parts = array("a", "b", "c"));

        $this->assertEquals(
            "c",
            $path->name()
        );

        $this->setExpectedException(get_class(new \UnderflowException()));
        $path = new Path(array());
        $path->name();
    }

    /**
     * @test
     */
    public function it_goes_down()
    {
        $path = new Path($parts = array("a", "b"));

        $this->assertEquals(
            new Path(array("a", "b", "c")),
            $path->down("c")
        );
    }

    /**
     * @test
     */
    public function it_goes_up()
    {
        $path = new Path($parts = array("a", "b", "c"));

        $this->assertEquals(
            new Path(array("a", "b")),
            $path->up()
        );
    }

    /**
     * @test
     */
    public function it_checks_if_root()
    {
        $path = new Path($parts = array("a", "b", "c"));

        $this->assertFalse($path->isRoot());

        $root = new Path();

        $this->assertTrue($root->isRoot());
    }

    /**
     * @test
     */
    public function it_find_ancestor()
    {
        $path1 = new Path(array("a", "b", "c"));
        $path2 = new Path(array("a", "b", "d", "e"));
        $ancestor = new Path(array("a", "b"));

        $this->assertEquals(
            $ancestor,
            $path1->ancestor($path2)
        );

        $this->assertEquals(
            $ancestor,
            $path2->ancestor($path1)
        );
    }

    /**
     * @test
     */
    public function it_gets_from()
    {
        $path = new Path(array("a", "b", "c", "d"));

        $this->assertEquals(
            new Path(array("c", "d")),
            $path->from(new Path(array("a", "b")))
        );

        $this->assertEquals(
            $path,
            $path->from(new Path(array("c", "b")))
        );

        $path = new Path(array("a"));

        $this->assertEquals(
            new Path(array()),
            $path->from(new Path(array("a")))
        );
    }

    /**
     * @test
     */
    public function it_converts_to_string()
    {
        $path = new Path(array("a", "b", "c"));

        $this->assertEquals(
            "a\\b\\c",
            $path->toString()
        );

        $this->assertEquals(
            "a.b.c",
            $path->toString(".")
        );
    }

    /**
     * @test
     */
    public function it_converts_to_string_with_leading_sep()
    {
        $path = new Path(array("a", "b", "c"));

        $this->assertEquals(
            "\\a\\b\\c",
            $path->toAbsoluteString()
        );

        $this->assertEquals(
            ".a.b.c",
            $path->toAbsoluteString(".")
        );
    }

    /**
     * @test
     */
    public function it_checks_if_prefix_of()
    {
        $path = new Path(array("a", "b", "c"));
        $path1 = new Path(array("a", "b"));
        $path2 = new Path(array("a", "b", "c", "d"));
        $path3 = new Path(array("a", "c"));

        $this->assertTrue($path1->isPrefixOf($path));
        $this->assertFalse($path2->isPrefixOf($path));
        $this->assertFalse($path3->isPrefixOf($path));
    }
}
