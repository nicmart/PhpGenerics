<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Code;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class GenericApplicationsTest extends GenericFunctionalTestCase
{
    /**
     * @test
     */
    public function it_works_with_optional_ops()
    {
        include __DIR__ . "/files/OptionOps.php";
    }

    /**
     * @test
     */
    public function it_works_with_optionals()
    {
        include __DIR__ . "/files/Option.php";
    }

    /**
     * @test
     */
    public function it_works_with_functions()
    {
        include __DIR__ . "/files/Function.php";
    }

    /**
     * @test
     */
    public function it_works_with_nested_types()
    {
        include __DIR__ . "/files/Nested.php";
    }

    /**
     * @test
     */
    public function it_works_with_nested_functions()
    {
        include __DIR__ . "/files/NestedFunction.php";
    }

    /**
     * @test
     */
    public function it_works_with_predicates()
    {
        include __DIR__ . "/files/Predicate.php";
    }

    /**
     * @test
     */
    public function it_works_with_tuples()
    {
        include __DIR__ . "/files/Tuple.php";
    }
}