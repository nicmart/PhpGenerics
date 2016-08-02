<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser\Parser;


use NicMart\Generics\AST\Parser\Parser;
use PhpParser\ParserAbstract as PhpParser;

/**
 * Class PhpParserParser
 * @package NicMart\Generics\Infrastructure\PhpParser\Parser
 */
class PhpParserParser implements Parser
{
    /**
     * @var PhpParser
     */
    private $parser;

    /**
     * PhpParserParser constructor.
     * @param PhpParser $parser
     */
    public function __construct(PhpParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param string $source
     * @return null|\PhpParser\Node[]
     */
    public function parse($source)
    {
        return $this->parser->parse($source);
    }
}