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
 * Class ChainTypeTransformer
 * @package NicMart\Generics\Type\Transformer
 */
class ChainTypeTransformer implements TypeTransformer
{
    /**
     * @var array|TypeTransformer[]
     */
    private $transformers;

    /**
     * ChainTypeTransformer constructor.
     * @param TypeTransformer[] $transformers
     */
    public function __construct(array $transformers = [])
    {
        $this->transformers = $transformers;
    }

    /**
     * @param Type $type
     * @return Type
     */
    public function transform(Type $type)
    {
        $transformedType = $type;

        foreach ($this->transformers as $transformer) {
            $transformedType = $transformer->transform($transformedType);
        }

        return $transformedType;
    }
}