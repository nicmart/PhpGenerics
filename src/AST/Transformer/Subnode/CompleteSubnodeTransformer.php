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
 * Class CompleteSubnodeTransformer
 * @package NicMart\Generics\AST\Transformer\Subnode
 */
class CompleteSubnodeTransformer extends AbstractSubnodeTransformer
{
    /**
     * @param Node $node
     * @return string[]
     */
    protected function subnodesNames(Node $node)
    {
        return $node->getSubNodeNames();
    }
}