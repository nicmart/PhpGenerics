<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 11/05/2016, 13:42
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Source\Dumper;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Source\SourceUnit;

class Psr0SourceUnitDumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_dumps_with_psr0_structure()
    {
        $path = "/tmp/generics";

        $dumper = new Psr0SourceUnitDumper($path);

        $sourceUnit = new SourceUnit(
            FullName::fromString("Ns1\\Ns2\\Class1"),
            '
                define(\'__GENERICS_INCLUDED\', 1);
            '
        );

        $result = $dumper->dump($sourceUnit);

        $filePath = $path . "/Ns1/Ns2/Class1.php";

        $this->assertFileExists($filePath);

        $this->assertEquals(
            "<?php\n\n" . $sourceUnit->source(),
            file_get_contents($filePath)
        );

        $this->assertEquals(
            new DumpResult(
                $filePath,
                $sourceUnit
            ),
            $result
        );

        unlink($filePath);
    }
}
