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
 * Interface SubnodeTransformer
 * @package NicMart\Generics\AST\Transformer\Subnode
 */
interface SubnodeTransformer
{
    /**
     * @param Node $node
     * @param callable $f
     * @return Node
     */
    public function map(Node $node, callable $f);
}