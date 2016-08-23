<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Serializer;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\Type;

/**
 * Interface TypeSerializer
 * @package NicMart\Generics\Type\Serializer
 */
interface TypeSerializer
{
    /**
     * @param Type $type
     * @return FullName
     */
    public function serialize(Type $type);
}