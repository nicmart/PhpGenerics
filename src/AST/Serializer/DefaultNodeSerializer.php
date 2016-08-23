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

use NicMart\Generics\AST\Parser\Parser;

/**
 * Class PhpParserNodeSerializer
 * @package NicMart\Generics\AST\Serializer
 */
class DefaultNodeSerializer implements NodeSerializer
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * PhpParserNodeSerializer constructor.
     * @param Parser $parser
     * @param Serializer $serializer
     */
    public function __construct(Parser $parser, Serializer $serializer)
    {
        $this->parser = $parser;
        $this->serializer = $serializer;
    }

    /**
     * @param string $phpSource
     * @return null|\PhpParser\Node[]
     */
    public function toNodes($phpSource)
    {
        return $this->parser->parse($phpSource);
    }

    /**
     * @param array $phpParserNodes
     * @return string
     */
    public function toSource(array $phpParserNodes)
    {
        return $this->serializer->serialize($phpParserNodes);
    }

}