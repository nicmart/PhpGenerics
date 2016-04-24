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

use NicMart\Generics\Source\Transformer\SourceTransformer;
use PhpParser\NodeTraverserInterface;
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
     * @var array|\PhpParser\NodeTraverserInterface[]
     */
    private $traversers;

    /**
     * @var Standard
     */
    private $prettyPrinter;

    /**
     * PhpParserSourceTransformer constructor.
     * @param Parser $parser
     * @param Standard $prettyPrinter
     * @param NodeTraverserInterface[] $traversers
     */
    public function __construct(
        Parser $parser,
        Standard $prettyPrinter,
        array $traversers
    ) {
        $this->parser = $parser;
        $this->traversers = $traversers;
        $this->prettyPrinter = $prettyPrinter;
    }

    /**
     * @param string $source
     * @return string
     */
    public function transform($source)
    {
        $nodes = $this->parser->parse($source);

        foreach ($this->traversers as $traverser) {
            $traverser->traverse($nodes);
        }

        return $this->prettyPrinter->prettyPrint($nodes);
    }
}