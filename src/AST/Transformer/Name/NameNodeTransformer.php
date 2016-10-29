<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer\Name;

use NicMart\Generics\AST\Transformer\NodeTransformer;
use PhpParser\Node;

class NameNodeTransformer implements NodeTransformer
{
    /**
     * @var callable
     */
    private $nameTransformer;

    /**
     * NameNodeTransformer constructor.
     * @param callable $nameTransformer
     */
    public function __construct(callable $nameTransformer)
    {
        $this->nameTransformer = $nameTransformer;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        $newNodes = array();
        foreach ($nodes as $node) {
            $newNodes[] = $node instanceof Node\Name
                ? call_user_func($this->nameTransformer, $node)
                : $node
            ;
        }

        return $newNodes;
    }
}