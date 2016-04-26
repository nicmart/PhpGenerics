<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Evaluation;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Source\SourceUnit;

class Psr0SourceUnitEvaluationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_saves_source()
    {
        $path = "/tmp/generics";

        $evaluation = new Psr0SourceUnitEvaluation($path);

        $sourceUnit = new SourceUnit(
            FullName::fromString("Ns1\\Ns2\\Class1"),
            '<?php
                define(\'__GENERICS_INCLUDED\', 1);
            '
        );

        $evaluation->evaluate($sourceUnit);

        $this->assertFileExists($path . "/Ns1/Ns2/Class1.php");

        $this->assertTrue(
            defined('__GENERICS_INCLUDED')
        );

        //unlink($path);
    }
}
