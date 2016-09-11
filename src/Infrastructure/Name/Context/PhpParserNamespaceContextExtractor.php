<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\Name\Context;


use NicMart\Generics\AST\Context\NamespaceContextNodeExtractor;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\NamespaceContextExtractor;
use PhpParser\Parser;

/**
 * Class PhpParserNamespaceContextExtractor
 * @package NicMart\Generics\Infrastructure\Name\Context
 */
class PhpParserNamespaceContextExtractor implements NamespaceContextExtractor
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NamespaceContextNodeExtractor
     */
    private $namespaceContextNodeExtractor;

    /**
     * PhpParserNamespaceContextExtractor constructor.
     * @param Parser $parser
     * @param NamespaceContextNodeExtractor $namespaceContextNodeExtractor
     */
    public function __construct(
        Parser $parser,
        NamespaceContextNodeExtractor $namespaceContextNodeExtractor
    ) {
        $this->parser = $parser;
        $this->namespaceContextNodeExtractor = $namespaceContextNodeExtractor;
    }

    /**
     * @param string $source
     * @return NamespaceContext
     */
    public function contextOf($source)
    {
        $statements = $this->parser->parse($source);

        return $this->namespaceContextNodeExtractor->extractFromArray($statements);
    }
}