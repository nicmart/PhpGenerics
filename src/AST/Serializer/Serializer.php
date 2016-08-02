<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Serializer;


/**
 * Interface Serializer
 * @package NicMart\Generics\AST\Serializer
 */
interface Serializer
{
    /**
     * @param array $nodes
     * @return string The source
     */
    public function serialize(array $nodes);
}