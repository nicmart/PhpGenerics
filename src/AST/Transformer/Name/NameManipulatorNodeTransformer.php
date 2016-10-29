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
use NicMart\Generics\Infrastructure\PhpParser\Name\NameManipulator;
use PhpParser\Node;

/**
 * Class NameManipulatorNodeTransformer
 * @package NicMart\Generics\AST\Transformer\Name
 */
class NameManipulatorNodeTransformer implements NodeTransformer
{
    /**
     * @var NameManipulator
     */
    private $nameManipulator;

    /**
     * @var callable
     */
    private $nameTransformer;

    /**
     * NameManipulatorNodeTransformer constructor.
     * @param NameManipulator $nameManipulator
     * @param callable $nameTransformer
     */
    public function __construct(
        NameManipulator $nameManipulator,
        callable $nameTransformer
    ) {
        $this->nameManipulator = $nameManipulator;
        $this->nameTransformer = $nameTransformer;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        $newNodes = [];
        $f = $this->nameTransformer;

        foreach ($nodes as $node) {
            $newNodes[] = $this($node);
        }

        return $newNodes;
    }

    /**
     * @param Node $node
     * @return Node
     */
    public function __invoke(Node $node)
    {
        if (!$this->nameManipulator->accept($node)) {
            return $node;
        }

        $f = $this->nameTransformer;

        return $this->nameManipulator->withName(
            $node,
            $f($this->nameManipulator->readName($node))
        );
    }
}