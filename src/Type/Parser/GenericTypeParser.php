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
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\Transformer\NameQualifier;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Type\VariableType;

/**
 * Class GenericTypeParser
 * @package NicMart\Generics\Type\Parser
 */
class GenericTypeParser implements TypeParser
{
    /**
     * @param Name $name
     * @param NameQualifier $nameQualifier
     * @return Type|void
     */
    public function parse(Name $name, NameQualifier $nameQualifier)
    {
        $fullName = $nameQualifier->qualify($name);

        if (PrimitiveType::isPrimitive($fullName)) {
            return new PrimitiveType($fullName);
        }

        if (VariableType::isVariable($fullName)) {
            return new VariableType($fullName);
        }
    }
}