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
}