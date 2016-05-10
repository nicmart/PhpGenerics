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


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Source\Compiler\GenericCompiler;
use NicMart\Generics\Source\Compiler\GenericCompilerFactory;

class TypeMapTest extends GenericFunctionalTestCase
{
    private $typeMap;

    /**
     * @var GenericCompiler
     */
    private $compiler;

    private $typeMapCompiler;
    
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

            ->when_i_run_compiler()

            ->i_should_have_classes_defined(array(
                "Ns\\Interface«FullName»",
                "Ns\\Interface«stdClass»",
                "Ns\\Class«FullName»",
                "Ns\\Class«stdClass»"
            ))
        ;
    }
    
    function i_define_a_map_like(array $map)
    {
        $map = new TypeMap();

        foreach ($map as $generic => $types) {
            $map = $map->withApplication(
                FullName::fromString($generic),
                array_map(
                    function ($type) { return FullName::fromString($type); },
                    $types
                )
            );
        }

        $this->typeMap = $map;

        return $this;
    }
    
    function i_have_the_code($code)
    {
        $this->include_($code);

        return $this;
    }

    function when_i_run_compiler()
    {
        return $this->typeMapCompiler->compile($this->typeMap);
    }

    function i_should_have_classes_defined(array $classes)
    {
        foreach ($classes as $class) {
            $this->assertTrue(
                class_exists($class)
            );
        }
    }


}
