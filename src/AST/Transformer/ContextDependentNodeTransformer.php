<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer;


use NicMart\Generics\AST\Context\NamespaceContextNodeExtractor;
use PhpParser\Node;

class ContextDependentNodeTransformer implements NodeTransformer
{
    /**
     * @var NamespaceContextNodeExtractor
     */
    private $contextNodeExtractor;
    /**
     * @var callable
     */
    private $factory;

    /**
     * ContextDependentNodeTransformer constructor.
     * @param NamespaceContextNodeExtractor $contextNodeExtractor
     * @param callable $factory A function NamespaceContext => NodeTransformer
     */
    public function __construct(
        NamespaceContextNodeExtractor $contextNodeExtractor,
        callable $factory
    ) {
        $this->contextNodeExtractor = $contextNodeExtractor;
        $this->factory = $factory;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        $factory = $this->factory;

        /** @var NodeTransformer $transformer */
        $transformer = $factory(
            $this->contextNodeExtractor->extractFromArray($nodes)
        );

        return $transformer->transformNodes($nodes);
    }
}