<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Code;


use NicMart\Generics\Map\GenericTypeApplication;
use NicMart\Generics\Map\TypeMap;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Source\Compiler\DefaultGenericCompiler;
use NicMart\Generics\Source\Compiler\GenericCompilerFactory;
use NicMart\Generics\Source\Dumper\Psr0SourceUnitDumper;
use NicMart\Generics\Source\Evaluation\IncludeDumpedSourceUnitEvaluation;
use NicMart\Generics\Source\Evaluation\SourceUnitEvaluation;

/**
 * Class TypeMapTest
 * @package NicMart\Generics\Code
 */
class TypeMapTest // extends GenericFunctionalTestCase
{
    /**
     * @var
     */
    private $typeMap;

    /**
     * @var DefaultGenericCompiler
     */
    private $compiler;

    /**
     * @var
     */
    private $typeMapCompiler;

    /**
     * @var SourceUnitEvaluation
     */
    private $evaluation;

    /**
     *
     */
    public function setUp()
    {
        $this->typeMap = array();

        $this->compiler = GenericCompilerFactory::compiler();

        $this->typeMapCompiler = TypeMapCompilerFactory::compiler();
    }

    /**
     * @notest
     */
    public function testMapLoading()
    {
        $this
            ->i_define_a_map_like(array(
                "Ns\\Class«T»" => array(
                    array("stdClass"),
                    array('\NicMart\Generics\Name\FullName')
                )
            ))

            ->i_have_the_code("
                namespace Ns;
                interfacee Interface«T» {}
            ")

            ->i_have_the_code("
                namespace Ns;
                class Class«T» implements Interface «T»{}
            ")

            ->i_have_evaulation(
                new IncludeDumpedSourceUnitEvaluation(
                    new Psr0SourceUnitDumper(__DIR__ . "/cache/generic")
                )
            )

            ->when_i_run_compiler()

            ->i_should_have_classes_defined(array(
                "Ns\\Interface«FullName»",
                "Ns\\Interface«stdClass»",
                "Ns\\Class«FullName»",
                "Ns\\Class«stdClass»"
            ))
        ;
    }

    /**
     * @param array $map
     * @return $this
     */
    function i_define_a_map_like(array $map)
    {
        $typeMap = new TypeMap();

        foreach ($map as $generic => $types) {
            $typeMap = $typeMap->withApplication(
                new GenericTypeApplication(
                    FullName::fromString($generic),
                    array_map(
                        function ($type) { return FullName::fromString($type); },
                        $types
                    )
                )
            );
        }

        $this->typeMap = $typeMap;

        return $this;
    }

    /**
     * @param $code
     * @return $this
     */
    function i_have_the_code($code)
    {
        $this->include_($code);

        return $this;
    }

    function i_have_evaulation(SourceUnitEvaluation $evaluation)
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * @return mixed
     */
    function when_i_run_compiler()
    {
        $this->typeMapCompiler->compile($this->typeMap);

        return $this;
    }

    /**
     * @param array $classes
     */
    function i_should_have_classes_defined(array $classes)
    {
        foreach ($classes as $class) {
            $this->assertTrue(
                class_exists($class)
            );
        }
    }


}
