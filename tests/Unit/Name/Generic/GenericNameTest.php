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

class GenericNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericName
     */
    private $generic;

    public function setUp()
    {
        $this->generic = new GenericName(
            FullName::fromString("Ns\\Class1"),
            array(
                FullName::fromString('\NicMart\Generics\Variable\T'),
                FullName::fromString('\NicMart\Generics\Variable\S'),
            )
        );
    }

    /**
     * @test
     */
    public function it_returns_main()
    {
        $this->assertEquals(
            FullName::fromString("Ns\\Class1"),
            $this->generic->main()
        );
    }

    /**
     * @test
     */
    public function it_returns_params()
    {
        $this->assertEquals(
            array(
                FullName::fromString('\NicMart\Generics\Variable\T'),
                FullName::fromString('\NicMart\Generics\Variable\S'),
            ),
            $this->generic->parameters()
        );
    }

    /**
     * @test
     */
    public function it_applies()
    {
        $applied = new GenericName(
            FullName::fromString("Ns\\Class1"),
            $params = array(
                FullName::fromString('\NicMart\Generics\Variable\T1'),
                FullName::fromString('\NicMart\Generics\Variable\S1'),
            )
        );

        $this->assertEquals(
            $applied,
            $this->generic->apply($params)
        );
    }

    /**
     * @test
     */
    public function it_returns_arity()
    {
        $this->assertEquals(
            2,
            $this->generic->arity()
        );
    }

    /**
     * @test
     */
    public function it_computes_assignments()
    {
        $this->assertEquals(
            NameAssignmentContext::fromStrings(array(
                '\NicMart\Generics\Variable\T' => 'A',
                '\NicMart\Generics\Variable\S' => 'B',
            )),
            $this->generic->assignments(array(
                FullName::fromString("A"),
                FullName::fromString("B")
            ))
        );
    }
}
