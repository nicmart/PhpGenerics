<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;
use PhpParser\Node;

/**
 * Class NameAdapter
 * @package NicMart\Generics\Infrastructure\PhpParser
 */
final class PhpNameAdapter
{
    /**
     * @param Node\Name $name
     * @return FullName|RelativeName
     */
    public function fromPhpName(Node\Name  $name)
    {
        if ($name->isFullyQualified()) {
            return new FullName($name->parts);
        }

        return new RelativeName($name->parts);
    }

    /**
     * @param Name $name
     * @return Node\Name|Node\Name\FullyQualified
     */
    public function toPhpName(Name $name)
    {
        return $name instanceof FullName && !$name->isNative()
            ? new Node\Name\FullyQualified($name->parts())
            : new Node\Name($name->parts())
        ;
    }
}