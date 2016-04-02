<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor;

use NicMart\Generics\AST\Visitor\Action\EnterNodeAction;
use NicMart\Generics\AST\Visitor\Action\LeaveNodeAction;
use PhpParser\Node;

/**
 * Interface Visitor
 * @package NicMart\Generics\AST\Visitor
 */
interface Visitor
{
    /**
     * @param Node $node
     * @return EnterNodeAction
     */
    public function enterNode(Node $node);

    /**
     * @param Node $node
     * @return LeaveNodeAction
     */
    public function leaveNode(Node $node);
}