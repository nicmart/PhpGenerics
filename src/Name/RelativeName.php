<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name;

use NicMart\Generics\Name\Context\NamespaceContext;

/**
 * Class RelativeType
 * @package NicMart\Generics\Name
 */
final class RelativeName extends Name
{
    /**
     * @var string[]
     */
    private $nativeTypes = array(
        "string",
        "int",
        "callable",
        "array",
        "resource",
        "float",
        "double",
        "bool",
        "void",

        "static",
        "self",
        "parent"
    );

    /**
     * @return bool
     */
    public function isNative()
    {
        $parts = $this->parts();
        return
            count($parts) == 1
            && in_array($parts[0], $this->nativeTypes)
        ;
    }

    /**
     * @return FullName
     */
    public function toFullName()
    {
        return new FullName($this->parts());
    }
}