<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor\Action;

use PhpParser\Node;

/**
 * Class ReplaceNodeWith
 * @package NicMart\Generics\AST\Visitor\Action
 */
class ReplaceNodeWith
{
    /**
     * @var Node
     */
    private $node;

    /**
     * ReplaceNodeWith constructor.
     * @param Node $node
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return Node
     */
    public function node()
    {
        return $this->node;
    }
}