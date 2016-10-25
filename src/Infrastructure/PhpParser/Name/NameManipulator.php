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
     * @param Node $useUse
     * @return mixed
     */
    public function readName(Node $useUse);

    /**
     * @param Node $useUse
     * @param Node\Name $name
     * @return mixed
     */
    public function withName(Node $useUse, Node\Name $name);
}