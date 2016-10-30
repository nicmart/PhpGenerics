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

use NicMart\Generics\AST\Transformer\Subnode\SubnodeTransformer;
use PhpParser\Node;

/**
 * Class BottomUpNodeTransformer
 * @package NicMart\Generics\AST\Transformer
 */
class BottomUpNodeTransformer implements NodeTransformer
{
    /**
     * @var SubnodeTransformer
     */
    private $subnodeTransformer;
    /**
     * @var callable
     */
    private $f;

    /**
     * BottomUpNodeTransformer constructor.
     * @param SubnodeTransformer $subnodeTransformer
     * @param callable $f
     */
    public function __construct(
        SubnodeTransformer $subnodeTransformer,
        callable $f
    ) {
        $this->subnodeTransformer = $subnodeTransformer;
        $this->f = $f;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        foreach ($nodes as &$node) {
            $node = $this($node);
        }

        return $nodes;
    }

    /**
     * @param Node $node
     * @return Node
     */
    public function __invoke(Node $node)
    {
        $f = $this->f;
        return $f($this->subnodeTransformer->map($node, $this));
    }
}