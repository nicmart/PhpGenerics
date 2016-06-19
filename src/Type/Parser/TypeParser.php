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
use NicMart\Generics\Name\Transformer\NameQualifier;
use NicMart\Generics\Type\Type;

/**
 * Interface TypeParser
 *
 * Convert a fullname to a type
 *
 * @package NicMart\Generics\Type\Parser
 *
 * @todo see GenericNameFactory
 */
interface TypeParser
{
    /**
     * @param FullName $name
     * @param NameQualifier $nameQualifier
     * @return Type
     */
    public function parse(FullName $name, NameQualifier $nameQualifier);
}