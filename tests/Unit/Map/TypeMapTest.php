<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Map;


use NicMart\Generics\Name\FullName;

class TypeMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_generic_types()
    {
        $map = new TypeMap(array(
            new TypeApplication(
                FullName::fromString("ns\\a"),
                array()
            ),
            new TypeApplication(
                FullName::fromString("ns\\b"),
                array()
            ),
            new TypeApplication(
                FullName::fromString("ns\\b"),
                array(FullName::fromString("c"))
            ),
        ));

        $this->assertEquals(
            array(
                FullName::fromString("ns\\a"),
                FullName::fromString("ns\\b")
            ),
            $map->genericTypes()
        );
    }

    /**
     * @test
     */
    public function it_returns_type_params()
    {
        $map = new TypeMap(array(
            $a = new TypeApplication(
                FullName::fromString("ns\\a"),
                array(
                    FullName::fromString("c"),
                    FullName::fromString("d"),
                )
            ),
            $b1 = new TypeApplication(
                FullName::fromString("ns\\b"),
                array(FullName::fromString("c"))
            ),
            $b2 = new TypeApplication(
                FullName::fromString("ns\\b"),
                array(FullName::fromString("d"))
            ),
        ));

        $this->assertEquals(
            array($a),
            $map->applications(FullName::fromString("ns\\a"))
        );

        $this->assertEquals(
            array($b1, $b2),
            $map->applications(FullName::fromString("ns\\b"))
        );

        $this->assertEquals(
            array(),
            $map->applications(FullName::fromString("ns\\undefined"))
        );
    }

    /**
     * @test
     */
    public function it_is_immutable()
    {
        $map = new TypeMap();
        $copy = clone $map;

        $map2 = $map->withApplication($app = new TypeApplication(
            FullName::fromString("ns\\b"),
            array(FullName::fromString("c"))
        ));

        $this->assertEquals(
            $copy,
            $map
        );

        $this->assertEquals(
            new TypeMap(array($app)),
            $map2
        );
    }
}
