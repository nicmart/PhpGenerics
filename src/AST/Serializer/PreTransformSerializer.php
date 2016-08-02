<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Serializer;


use NicMart\Generics\AST\Transformer\NodeTransformer;

/**
 * Class PreTransformSerializer
 * @package NicMart\Generics\AST\Serializer
 */
class PreTransformSerializer implements Serializer
{
    /**
     * @var NodeTransformer
     */
    private $transformer;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * PreTransformSerializer constructor.
     * @param NodeTransformer $transformer
     * @param Serializer $serializer
     */
    public function __construct(
        NodeTransformer $transformer,
        Serializer $serializer
    ) {
        $this->transformer = $transformer;
        $this->serializer = $serializer;
    }

    /**
     * @param array $nodes
     * @return string
     */
    public function serialize(array $nodes)
    {
        return $this->serializer->serialize(
            $this->transformer->transformNodes($nodes)
        );
    }
}