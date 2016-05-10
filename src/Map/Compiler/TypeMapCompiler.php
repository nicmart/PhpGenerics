<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 11/05/2016, 12:43
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Map\Compiler;

use NicMart\Generics\Map\TypeMap;

/**
 * Interface TypeMapCompiler
 * @package NicMart\Generics\Map\Compiler
 */
interface TypeMapCompiler
{
    /**
     * @param TypeMap $typeMap
     * @return void
     */
    public function compile(TypeMap $typeMap);
}