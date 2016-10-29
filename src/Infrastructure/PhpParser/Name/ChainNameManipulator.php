<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser\Name;

use PhpParser\Node;

class ChainNameManipulator implements NameManipulator
{
    /**
     * @var NameManipulator[]
     */
    private $manipulators = [];

    /**
     * ChainNameManipulator constructor.
     * @param NameManipulator[] $nameManipulators
     */
    public function __construct(array $nameManipulators)
    {
        foreach ($nameManipulators as $nameManipulator) {
            $this->addManipulator($nameManipulator);
        }
    }

    /**
     * @param Node $node
     * @return bool
     */
    public function accept(Node $node)
    {
        foreach ($this->manipulators as $manipulator) {
            if ($manipulator->accept($node)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Node $node
     * @return Node\Name
     */
    public function readName(Node $node)
    {
        foreach ($this->manipulators as $manipulator) {
            if ($manipulator->accept($node)) {
                return $manipulator->readName($node);
            }
        }
    }

    /**
     * @param Node $node
     * @param Node\Name $name
     * @return Node
     */
    public function withName(Node $node, Node\Name $name)
    {
        foreach ($this->manipulators as $manipulator) {
            if ($manipulator->accept($node)) {
                return $manipulator->withName($node, $name);
            }
        }

        return $node;
    }

    /**
     * @param NameManipulator $nameManipulator
     */
    private function addManipulator(NameManipulator $nameManipulator)
    {
        $this->manipulators[] = $nameManipulator;
    }
}