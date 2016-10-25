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
use PhpParser\Node\Name\FullyQualified;

/**
 * Class UseUseNameManipulator
 * @package NicMart\Generics\Infrastructure\PhpParser\Name
 */
class UseUseNameManipulator implements NameManipulator
{

    /**
     * @param Node|Node\Stmt\UseUse $useUse
     * @return mixed
     */
    public function readName(Node $useUse)
    {
        return new FullyQualified($useUse->name->parts);
    }

    /**
     * @param Node|Node\Stmt\UseUse $useUse
     * @param Node\Name $name
     * @return mixed
     */
    public function withName(Node $useUse, Node\Name $name)
    {
        $oldName = $useUse->name;
        $useUse->name = $name;
        if ($useUse->alias == $oldName->getLast()) {
            $useUse->alias = $name->getLast();
        }

        return $useUse;
    }
}