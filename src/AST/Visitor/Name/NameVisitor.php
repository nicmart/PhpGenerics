<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Visitor\Name;

use NicMart\Generics\AST\Visitor\Action\EnterNodeAction;
use PhpParser\Node;

/**
 * Interface NameVisitor
 * @package NicMart\Generics\AST\Visitor\Name
 */
interface NameVisitor
{
    /**
     * @param Node\Name $name
     * @return EnterNodeAction
     */
    public function visitName(Node\Name $name);
}