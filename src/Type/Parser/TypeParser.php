<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Parser;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\Type;

/**
 * Interface TypeParser
 *
 * Convert a fullname to a type
 *
 * @package NicMart\Generics\Type\Parser
 */
interface TypeParser
{
    /**
     * @param FullName $name
     * @return Type
     */
    public function parse(FullName $name);
}