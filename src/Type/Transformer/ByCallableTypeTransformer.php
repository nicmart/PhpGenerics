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
 * Class ByCallableTypeTransformer
 * @package NicMart\Generics\Type\Transformer
 */
class ByCallableTypeTransformer implements TypeTransformer
{
    /**
     * @var
     */
    private $typeTransformerCallable;

    /**
     * ByCallableTypeTransformer constructor.
     * @param $typeTransformerCallable
     */
    public function __construct($typeTransformerCallable)
    {
        if (!is_callable($typeTransformerCallable)) {
            throw new \InvalidArgumentException(
                __CLASS__ . " accepts only valid php callables"
            );
        }

        $this->typeTransformerCallable = $typeTransformerCallable;
    }

    /**
     * @param Type $type
     * @return Type
     */
    public function transform(Type $type)
    {
        return call_user_func($this->typeTransformerCallable, $type);
    }
}