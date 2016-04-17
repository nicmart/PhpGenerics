<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;

/**
 * Interface NameSimplifier
 * @package NicMart\Generics\Name\Transformer
 */
interface NameSimplifier
{
    /**
     * @param FullName $fullName
     * @return RelativeName
     */
    public function simplify(FullName $fullName);
}