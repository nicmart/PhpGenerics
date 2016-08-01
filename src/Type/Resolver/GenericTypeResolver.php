<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Resolver;


use NicMart\Generics\Type\ParametrizedType;

/**
 * Interface GenericTypeResolver
 * @package NicMart\Generics\Type\Resolver
 */
interface GenericTypeResolver
{
    /**
     * @param ParametrizedType $parametrizedType
     * @return mixed
     */
    public function toGenericType(ParametrizedType $parametrizedType);
}