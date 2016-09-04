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
use NicMart\Generics\Source\Dumper\Psr0SourceUnitDumper;
use NicMart\Generics\Source\SourceUnit;
use NicMart\Generics\Type\SimpleReferenceType;

class IncludeDumpedSourceUnitEvaluationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_saves_source()
    {
        $path = "/tmp/generics";

        $evaluation = new IncludeDumpedSourceUnitEvaluation(
            new Psr0SourceUnitDumper($path)
        );

        $sourceUnit = new SourceUnit(
            new SimpleReferenceType(FullName::fromString("dummy")),
            FullName::fromString("Ns1\\Ns2\\Class1"), '
                define(\'__GENERICS_INCLUDED\', 1);
            '
        );

        $filename = $path . "/Ns1/Ns2/Class1.php";

        $evaluation->evaluate($sourceUnit);

        $this->assertFileExists($filename);

        $this->assertTrue(
            defined('__GENERICS_INCLUDED')
        );

        unlink($filename);
    }
}
