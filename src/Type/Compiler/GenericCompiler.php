<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Compiler;

use NicMart\Generics\Source\SourceUnit;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;

/**
 * Interface GenericCompiler
 * @package NicMart\Generics\Type\Compiler
 */
interface GenericCompiler
{
    /**
     * @param GenericType $genericType
     * @param ParametrizedType $parametrizedType
     * @param SourceUnit $sourceUnit
     * @return SourceUnit
     */
    public function compile(
        GenericType $genericType,
        ParametrizedType $parametrizedType,
        SourceUnit $sourceUnit
    );
}