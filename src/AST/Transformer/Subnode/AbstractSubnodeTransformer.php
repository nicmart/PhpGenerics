<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer\Subnode;

use PhpParser\Node;

/**
 * Class AbstractSubnodeTransformer
 * @package NicMart\Generics\AST\Transformer\Subnode
 */
abstract class AbstractSubnodeTransformer implements SubnodeTransformer
{
    /**
     * @param Node $node
     * @return string[]
     */
    abstract protected function subnodesNames(Node $node);

    /**
     * @param Node $node
     * @param callable $f
     * @return Node
     */
    public function map(Node $node, callable $f)
    {
        $node = clone $node;

        foreach ($this->subnodesNames($node) as $subNodeName) {
            $subNode = $node->$subNodeName;

            $node->$subNodeName = is_array($subNode)
                ? $this->mapArray($subNode, $f)
                : $this->mapAny($subNode, $f)
            ;
        }

        return $node;
    }

    private function mapArray(array $nodes, callable $f)
    {
        foreach ($nodes as &$node) {
            $node = $this->mapAny($node, $f);
        }

        return $nodes;
    }

    /**
     * @param $nodeOrSomethingElse
     * @param callable $f
     * @return mixed
     */
    private function mapAny($nodeOrSomethingElse, callable $f)
    {
        return $nodeOrSomethingElse instanceof Node
            ? $f($nodeOrSomethingElse)
            : $nodeOrSomethingElse
        ;
    }
}