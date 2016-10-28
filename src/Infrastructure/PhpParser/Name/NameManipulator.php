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
 * Interface NameManipulator
 * @package NicMart\Generics\Infrastructure\PhpParser\Name
 */
interface NameManipulator
{
    /**
     * @param Node $node
     * @return bool
     */
    public function accept(Node $node);

    /**
     * @param Node $node
     * @return Node\Name
     */
    public function readName(Node $node);

    /**
     * @param Node $node
     * @param Node\Name $name
     * @return Node
     */
    public function withName(Node $node, Node\Name $name);
}