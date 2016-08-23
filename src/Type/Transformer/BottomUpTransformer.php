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


use NicMart\Generics\Type\Type;

/**
 * Class BottomUpTransformer
 *
 * Given a partial leaf-only transformer, transform it to a bottom-up transformer
 *
 * @package NicMart\Generics\Type\Transformer
 */
class BottomUpTransformer implements TypeTransformer
{
    /**
     * @var TypeTransformer
     */
    private $transformer;

    /**
     * BottomUpTransformer constructor.
     * @param TypeTransformer $transformer
     */
    public function __construct(TypeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param Type $type
     * @return Type
     */
    public function transform(Type $type)
    {
        return $this->transformer->transform($type->map($this));
    }
}