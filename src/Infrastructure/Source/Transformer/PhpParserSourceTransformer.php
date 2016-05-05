<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\Source\Transformer;

use NicMart\Generics\AST\Transformer\NodeTransformer;
use NicMart\Generics\Source\Transformer\SourceTransformer;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Serializer;

class PhpParserSourceTransformer implements SourceTransformer
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Standard
     */
    private $prettyPrinter;

    /**
     * @var \NicMart\Generics\Infrastructure\AST\Transformer\NodeTransformer
     */
    private $nodeTransformer;

    /**
     * PhpParserSourceTransformer constructor.
     * @param Parser $parser
     * @param NodeTransformer $nodeTransformer
     * @param Standard $prettyPrinter
     */
    public function __construct(
        Parser $parser,
        NodeTransformer $nodeTransformer,
        Standard $prettyPrinter
    ) {
        $this->parser = $parser;
        $this->prettyPrinter = $prettyPrinter;
        $this->nodeTransformer = $nodeTransformer;
    }

    /**
     * @param string $source
     * @return string
     */
    public function transform($source)
    {
        $nodes = $this->parser->parse($source);

        $nodes = $this->nodeTransformer->transformNodes($nodes);

        return $this->prettyPrinter->prettyPrint($nodes);
    }
}