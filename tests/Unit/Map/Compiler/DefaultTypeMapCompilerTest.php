<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 11/05/2016, 12:51
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Map\Compiler;


use NicMart\Generics\Map\GenericTypeApplication;
use NicMart\Generics\Map\TypeMap;
use NicMart\Generics\Name\FullName;

/**
 * Class DefaultTypeMapCompilerTest
 * @package NicMart\Generics\Map\Compiler
 * @
 */
class DefaultTypeMapCompilerTest // extends \PHPUnit_Framework_TestCase
{
    /**
     * @
     * @test
     */
    public function it_compiles()
    {
        $map = new TypeMap();
        $map = $map
            ->withApplication(new GenericTypeApplication(
                $genericName = FullName::fromString("Ns\\Class1«T·S»"),
                $typeParams = array(
                    FullName::fromString("stdClass"),
                    FullName::fromString('\NicMart\Generics\Name\FullName')
                )
            ))
        ;

        $genericCompiler = $this->getMock(
            '\NicMart\Generics\Source\Compiler\GenericCompiler'
        );

        $genericCompiler;


        $compiler = new DefaultTypeMapCompiler(

        );

        $compiler->compile($map);
    }
}
