<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\AST\Transformer;

use NicMart\Generics\Infrastructure\AST\Transformer\NodeTransformer;
use PhpParser\Node;

/**
 * Class ChainNodeTransformer
 * @package NicMart\Generics\Infrastructure\PhpParser\Transformer
 */
class ChainNodeTransformer implements NodeTransformer
{
    /**
     * @var NodeTransformer[]
     */
    private $transformers;

    /**
     * ChainNodeTransformer constructor.
     *
     * @param NodeTransformer[] $transformers
     */
    public function __construct(array $transformers)
    {
        foreach ($transformers as $transformer) {
            $this->addTransformer($transformer);
        }
    }

    /**
     * @param Node[] $nodes
     *
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        foreach ($this->transformers as $transformer) {
            $nodes = $transformer->transformNodes($nodes);
        }

        return $nodes;
    }

    /**
     * @param NodeTransformer $nodeTransformer
     */
    private function addTransformer(NodeTransformer $nodeTransformer)
    {
        $this->transformers[] = $nodeTransformer;
    }
}