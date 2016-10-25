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

/**
 * Class ClassNameManipulator
 *
 * Manipulate classes, interfaces and traits names
 *
 * @package NicMart\Generics\Infrastructure\PhpParser\Name
 */
class ClassNameManipulator implements NameManipulator
{
    /**
     * @param Node|Node\Stmt\Class_|Node\Stmt\Interface_|Node\Stmt\Trait_ $node
     * @return Node\Name
     */
    public function readName(Node $node)
    {
        return new Node\Name\Relative($node->name);
    }

    /**
     * @param Node|Node\Stmt\Class_|Node\Stmt\Interface_|Node\Stmt\Trait_ $node
     * @param Node\Name $name
     * @return Node
     */
    public function withName(Node $node, Node\Name $name)
    {
        $node->name = $name->getLast();
        
        return $node;
    }
}