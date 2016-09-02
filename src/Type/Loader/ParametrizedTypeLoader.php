<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Loader;


use NicMart\Generics\Type\Compiler\CompilationResult;
use NicMart\Generics\Type\ParametrizedType;

/**
 * Interface ParametrizedTypeLoader
 * @package NicMart\Generics\Type\Loader
 */
interface ParametrizedTypeLoader
{
    /**
     * @param ParametrizedType $parametrizedType
     * @return CompilationResult
     */
    public function load(ParametrizedType $parametrizedType);
}