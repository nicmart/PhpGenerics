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

use NicMart\Generics\AST\NodesList;

/**
 * Class ReplaceNodeWithList
 * @package NicMart\Generics\AST\Visitor\Action
 */
class ReplaceNodeWithList implements LeaveNodeAction
{
    /**
     * @var NodesList
     */
    private $nodesList;

    /**
     * ReplaceNodeWithList constructor.
     * @param NodesList $nodesList
     */
    public function __construct(NodesList $nodesList)
    {
        $this->nodesList = $nodesList;
    }

    /**
     * @return NodesList
     */
    public function nodeList()
    {
        return $this->nodeList();
    }
}