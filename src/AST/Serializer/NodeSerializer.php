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
 * Interface NodeSerializer
 * @package NicMart\Generics\AST\Serializer
 */
interface NodeSerializer
{
    /**
     * @param string $phpSource
     * @return array
     */
    public function toNodes($phpSource);

    /**
     * @param array $phpParserNodes
     * @return array
     */
    public function toSource(array $phpParserNodes);
}