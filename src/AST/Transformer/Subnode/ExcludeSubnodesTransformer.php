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
 * Class ExcludeSubnodesTransformer
 * @package NicMart\Generics\AST\Transformer\Subnode
 */
class ExcludeSubnodesTransformer extends AbstractSubnodeTransformer
{
    /**
     * @var array
     */
    private $subnodes = [];

    /**
     * ExcludeSubnodesTransformer constructor.
     * @param array $subNodes
     */
    public function __construct(array $subNodes)
    {
        $this->subnodes = $subNodes;
    }

    /**
     * @param Node $node
     * @return array
     */
    protected function subnodesNames(Node $node)
    {
        return array_diff($node->getSubNodeNames(), $this->subnodes);
    }
}