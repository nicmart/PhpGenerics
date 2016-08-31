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

final class ListenerTypeTransformer implements TypeTransformer
{
    /**
     * @var TypeTransformer
     */
    private $transformer;
    /**
     * @var callable
     */
    private $listener;

    /**
     * TrackerTypeTransformer constructor.
     * @param TypeTransformer $transformer
     * @param callable $listener
     */
    public function __construct(TypeTransformer $transformer, callable $listener)
    {
        $this->transformer = $transformer;
        $this->listener = $listener;
    }

    /**
     * @param Type $type
     * @return Type
     */
    public function transform(Type $type)
    {
        $transformedType = $this->transformer->transform($type);

        call_user_func($this->listener, $transformedType);

        return $transformedType;
    }
}