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
 * Class NameNameManipulator
 * @package NicMart\Generics\Infrastructure\PhpParser\Name
 */
class NameNameManipulator implements NameManipulator
{

    /**
     * @param Node|Node\Name $nameNode
     * @return mixed
     */
    public function readName(Node $nameNode)
    {
        return $nameNode;
    }

    /**
     * @param Node|Node\Name $nameNode
     * @param Node\Name $name
     * @return mixed
     */
    public function withName(Node $nameNode, Node\Name $name)
    {
        return $name;
    }
}