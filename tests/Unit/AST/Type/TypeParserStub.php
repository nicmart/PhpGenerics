<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Type;


use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\Transformer\NameQualifier;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\Type;

class TypeParserStub implements TypeParser
{
    /**
     * @param Name $name
     * @param NameQualifier $nameQualifier
     * @return Type
     */
    public function parse(Name $name, NameQualifier $nameQualifier)
    {
        return new SimpleReferenceType(
            $nameQualifier->qualify(
                new RelativeName($name->parts())
            )
        );
    }
}