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
use NicMart\Generics\Name\Generic\AngleQuotedGenericName;

/**
 * Class AngleQuotedGenericNameFactory
 * @package NicMart\Generics\Name\Generic\Factory
 */
class AngleQuotedGenericNameFactory implements GenericNameFactory
{
    /**
     * @param FullName $name
     * @return bool
     */
    public function isGeneric(FullName $name)
    {
        return strpos($name->toString(), "«") !== false;
    }

    /**
     * @param FullName $name
     * @return AngleQuotedGenericName
     */
    public function toGeneric(FullName $name)
    {
        return new AngleQuotedGenericName($name);
    }
}