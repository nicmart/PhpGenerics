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

use NicMart\Generics\GenericFunctionalTestCase;

/**
 * @requires PHP 7.0
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class MainPHP7FunctionalTest extends GenericFunctionalTestCase
{
    /**
     * @test
     */
    public function it_works_with_optional_ops()
    {
        include __DIR__ . "/files-php7/OptionOps.php";
    }

    /**
     * @test
     */
    public function it_works_with_optionals()
    {
        include __DIR__ . "/files-php7/Option.php";
    }

    /**
     * @test
     */
    public function it_works_with_functions()
    {
        include __DIR__ . "/files-php7/Function.php";
    }

    /**
     * @test
     */
    public function it_works_with_nested_types()
    {
        include __DIR__ . "/files-php7/Nested.php";
    }

    /**
     * @test
     */
    public function it_works_with_nested_functions()
    {
        include __DIR__ . "/files-php7/NestedFunction.php";
    }

    /**
     * @test
     */
    public function it_works_with_predicates()
    {
        include __DIR__ . "/files-php7/Predicate.php";
    }

    /**
     * @test
     */
    public function it_works_with_tuples()
    {
        include __DIR__ . "/files-php7/Tuple.php";
    }
}