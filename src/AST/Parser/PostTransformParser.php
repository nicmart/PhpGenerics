<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Parser;


use NicMart\Generics\AST\Transformer\NodeTransformer;

/**
 * Class PostTransformParser
 *
 * Apply a transformation to the nodes before the parsing
 *
 * @package NicMart\Generics\AST\Parser
 */
class PostTransformParser implements Parser
{
    /**
     * @var NodeTransformer
     */
    private $transformer;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * PostTransformParser constructor.
     * @param Parser $parser
     * @param NodeTransformer $transformer
     */
    public function __construct(Parser $parser, NodeTransformer $transformer)
    {
        $this->transformer = $transformer;
        $this->parser = $parser;
    }

    /**
     * @param string $source
     * @return \PhpParser\Node[]
     */
    public function parse($source)
    {
        return $this->transformer->transformNodes(
            $this->parser->parse($source)
        );
    }
}