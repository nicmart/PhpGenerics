<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics;

use NicMart\Generics\Example\PHP5\Option\Option«T»;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class MainPHP5FunctionalTest extends GenericFunctionalTestCase
{
    /**
     * @test
     */
    public function it_works_with_optional_ops()
    {
        include __DIR__ . "/files-php5/OptionOps.php";
    }

    /**
     * @test
     */
    public function it_works_with_optionals()
    {
        include __DIR__ . "/files-php5/Option.php";
    }

    /**
     * @test
     */
    public function it_works_with_functions()
    {
        include __DIR__ . "/files-php5/Function.php";
    }

    /**
     * @test
     */
    public function it_works_with_nested_types()
    {
        include __DIR__ . "/files-php5/Nested.php";
    }

    /**
     * @test
     */
    public function it_works_with_nested_functions()
    {
        include __DIR__ . "/files-php5/NestedFunction.php";
    }

    /**
     * @test
     */
    public function it_works_with_predicates()
    {
        include __DIR__ . "/files-php5/Predicate.php";
    }

    /**
     * @test
     */
    public function it_works_with_tuples()
    {
        include __DIR__ . "/files-php5/Tuple.php";
    }

    /**
     * @test
     */
    public function it_works_with_edge_cases()
    {
        interface_exists('\NicMart\Generics\Example\PHP5\Option\Option«T»');
    }
}