<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST;

use PhpParser\Node;

/**
 * Class NodesList
 * @package NicMart\Generics\AST
 */
final class NodesList
{
    /**
     * @var Node[]
     */
    private $nodes;

    /**
     * NodesList constructor.
     * @param Node[] $nodes
     */
    public function __construct(array $nodes)
    {
        foreach ($nodes as $node) {
            $this->addNode($node);
        }
    }

    /**
     * @return Node[]
     */
    public function nodes()
    {
        return $this->nodes;
    }

    /**
     * @param Node $node
     * @return NodesList
     */
    public function append(Node $node)
    {
        $new = clone $this;

        $new->nodes[] = $node;

        return $new;
    }

    /**
     * @param NodesList $list
     * @return NodesList
     */
    public function appendList(NodesList $list)
    {
        $new = clone $this;

        $new->nodes = array_merge($new->nodes, $list->nodes);

        return $new;
    }

    /**
     * @param Node $node
     * @return NodesList
     */
    public function prepend(Node $node)
    {
        $new = clone $this;

        array_unshift($new->nodes, $node);

        return $new;
    }

    /**
     * @param NodesList $nodeList
     * @return NodesList
     */
    public function prependList(NodesList $nodeList)
    {
        return $nodeList->appendList($this);
    }

    /**
     * @param Node $node
     */
    private function addNode(Node $node)
    {
        $this->nodes[] = $node;
    }
}