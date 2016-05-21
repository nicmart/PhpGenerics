<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic\Factory;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Name\Transformer\NameQualifier;

/**
 * Interface GenericNameFactory
 * @package NicMart\Generics\Name\Generic\Factory
 */
interface GenericNameFactory
{
    /**
     * @param FullName $name
     *
     * @return bool
     */
    public function isGeneric(FullName $name);

    /**
     * @param FullName $name
     * @param NameQualifier $qualifier
     *
     * @return GenericName
     */
    public function toGeneric(
        FullName $name,
        NameQualifier $qualifier
    );

    /**
     * @param GenericName $genericName
     * @return FullName
     */
    public function fromGeneric(GenericName $genericName);
}