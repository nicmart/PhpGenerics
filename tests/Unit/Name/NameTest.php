<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 05/04/2016, 14:43
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name;

/**
 * Class NameTest
 * @package NicMart\Generics\Name
 */
class NameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_sets_parts()
    {
        $name = new FullName($parts = array("a", "b", "c"));

        $this->assertEquals($parts, $name->parts());
    }

    /**
     * @test
     */
    public function it_gets_length()
    {
        $name = new FullName($parts = array("a", "b", "c"));

        $this->assertEquals(
            count($parts),
            $name->length()
        );
    }

    /**
     * @test
     */
    public function it_gets_name()
    {
        $name = new FullName($parts = array("a", "b", "c"));

        $this->assertEquals(
            new SimpleName("c"),
            $name->last()
        );

        $this->setExpectedException(get_class(new \UnderflowException()));
        $name = new FullName(array());
        $name->last();
    }

    /**
     * @test
     */
    public function it_goes_down()
    {
        $name = new FullName($parts = array("a", "b"));

        $this->assertEquals(
            new FullName(array("a", "b", "c")),
            $name->down("c")
        );
    }

    /**
     * @test
     */
    public function it_goes_up()
    {
        $name = new FullName($parts = array("a", "b", "c"));

        $this->assertEquals(
            new FullName(array("a", "b")),
            $name->up()
        );
    }

    /**
     * @test
     */
    public function it_checks_if_root()
    {
        $name = new FullName($parts = array("a", "b", "c"));

        $this->assertFalse($name->isRoot());

        $root = FullName::root();

        $this->assertTrue($root->isRoot());
    }

    /**
     * @test
     */
    public function it_find_ancestor()
    {
        $name1 = new FullName(array("a", "b", "c"));
        $name2 = new FullName(array("a", "b", "d", "e"));
        $ancestor = new FullName(array("a", "b"));

        $this->assertEquals(
            $ancestor,
            $name1->ancestor($name2)
        );

        $this->assertEquals(
            $ancestor,
            $name2->ancestor($name1)
        );
    }

    /**
     * @test
     */
    public function it_gets_from()
    {
        $name = new FullName(array("a", "b", "c", "d"));

        $this->assertEquals(
            new FullName(array("c", "d")),
            $name->from(new FullName(array("a", "b")))
        );

        $this->assertEquals(
            $name,
            $name->from(new FullName(array("c", "b")))
        );

        $name = new FullName(array("a"));

        $this->assertEquals(
            new FullName(array()),
            $name->from(new FullName(array("a")))
        );
    }

    /**
     * @test
     */
    public function it_converts_to_string()
    {
        $name = new FullName(array("a", "b", "c"));

        $this->assertEquals(
            "a\\b\\c",
            $name->toString()
        );

        $this->assertEquals(
            "a.b.c",
            $name->toString(".")
        );
    }

    /**
     * @test
     */
    public function it_converts_to_string_with_leading_sep()
    {
        $name = new FullName(array("a", "b", "c"));

        $this->assertEquals(
            "\\a\\b\\c",
            $name->toAbsoluteString()
        );

        $this->assertEquals(
            ".a.b.c",
            $name->toAbsoluteString(".")
        );
    }

    /**
     * @test
     */
    public function it_checks_if_prefix_of()
    {
        $name = new FullName(array("a", "b", "c"));
        $name1 = new FullName(array("a", "b"));
        $name2 = new FullName(array("a", "b", "c", "d"));
        $name3 = new FullName(array("a", "c"));

        $this->assertTrue($name1->isPrefixOf($name));
        $this->assertFalse($name2->isPrefixOf($name));
        $this->assertFalse($name3->isPrefixOf($name));
    }
}
