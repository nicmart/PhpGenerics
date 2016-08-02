<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser;

use NicMart\Generics\AST\Serializer\Serializer;

/**
 * Class PhpParserSerializer
 * @package NicMart\Generics\Infrastructure\PhpParser
 */
class PhpParserSerializer implements Serializer
{
    /**
     * @var PhpParserSerializer
     */
    private $phpParserSerializer;

    /**
     * PhpParserSerializer constructor.
     * @param PhpParserSerializer $phpParserSerializer
     */
    public function __construct(PhpParserSerializer $phpParserSerializer)
    {
        $this->phpParserSerializer = $phpParserSerializer;
    }

    /**
     * @param array $nodes
     * @return mixed|string
     */
    public function serialize(array $nodes)
    {
        return $this->phpParserSerializer->serialize($nodes);
    }
}