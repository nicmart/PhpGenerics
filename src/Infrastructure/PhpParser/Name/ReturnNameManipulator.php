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

class ReturnNameManipulator implements NameManipulator
{
    /**
     * @param Node|Node\FunctionLike $node
     * @return Node\Name
     */
    public function readName(Node $node)
    {
        return $node->getReturnType();
    }

    /**
     * @param Node|Node\FunctionLike $node
     * @param Node\Name $name
     * @return Node
     */
    public function withName(Node $node, Node\Name $name)
    {
        $node->returnType = $name;

        return $node;
    }

    /**
     * @param Node $node
     * @return bool
     */
    public function accept(Node $node)
    {
        return $node instanceof Node\FunctionLike;
    }
}