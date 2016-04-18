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
 * Class RelativeType
 * @package NicMart\Generics\Name
 */
final class RelativeName extends Name
{
    /**
     * @return FullName
     */
    public function toFullName()
    {
        return new FullName($this->parts());
    }

    /**
     * @return bool
     */
    public function isFullName()
    {
        return false;
    }
}