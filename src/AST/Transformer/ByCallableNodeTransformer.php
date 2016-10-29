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

use PhpParser\Node;

/**
 * Class ByCallableNodeTransformer
 * @package NicMart\Generics\AST\Transformer
 */
class ByCallableNodeTransformer implements NodeTransformer
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * ByCallableNodeTransformer constructor.
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        $newNodes = [];

        foreach ($nodes as $node) {
            $newNodes[] = $node instanceof Node
                ? $this->__invoke($node)
                : $node
            ;
        }

        return $newNodes;
    }

    /**
     * @param Node $node
     * @return mixed
     */
    public function __invoke(Node $node)
    {
        return call_user_func($this->callable, $node);
    }
}