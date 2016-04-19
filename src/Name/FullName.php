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

/**
 * Class Type
 * @package NicMart\Generics\Name
 */
final class FullName extends Name
{
    /**
     * @return RelativeName
     */
    public function toRelative()
    {
        return new RelativeName($this->parts());
    }


    /**
     * @return bool
     */
    public function isFullName()
    {
        return true;
    }
}