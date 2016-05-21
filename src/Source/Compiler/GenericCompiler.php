<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 11/05/2016, 12:50
 * Copyright (C) DXI Ltd
 */
namespace NicMart\Generics\Source\Compiler;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Source\SourceUnit;


/**
 * Class GenericCompiler
 * @package NicMart\Generics\Source\Compiler
 */
interface GenericCompiler
{
    /**
     * @param FullName $genericNameName
     * @param FullName[] $typeParameters
     * @return SourceUnit
     */
    public function compile(FullName $genericNameName, array $typeParameters);
}