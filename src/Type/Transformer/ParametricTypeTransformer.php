<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Transformer;


use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Type;

/**
 * Class ParametricTypeTransformer
 * @package NicMart\Generics\Type\Transformer
 */
class ParametricTypeTransformer implements TypeTransformer
{
    /**
     * @var GenericType
     */
    private $genericType;
    /**
     * @var ParametrizedType
     */
    private $parametrizedType;

    /**
     * ParametricTypeTransformer constructor.
     * @param GenericType $genericType
     * @param ParametrizedType $parametrizedType
     */
    public function __construct(
        GenericType $genericType,
        ParametrizedType $parametrizedType
    ) {
        $this->genericType = $genericType;
        $this->parametrizedType = $parametrizedType;
    }

    public function transform(Type $type)
    {
        // TODO: Implement transform() method.
    }
}