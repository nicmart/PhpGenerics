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


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Type;

/**
 * Class TypeSerializerStub
 * @package NicMart\Generics\AST\Type
 */
class TypeSerializerStub implements TypeSerializer
{
    /**
     * @param Type $type
     * @return FullName
     */
    public function serialize(Type $type)
    {
        return $type->name();
    }
}