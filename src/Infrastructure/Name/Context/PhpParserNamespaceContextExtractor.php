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


use NicMart\Generics\Adapter\PhpParserVisitorAdapter;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\NamespaceContextExtractor;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
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
     * @var NamespaceContextVisitor
     */
    private $visitor;

    /**
     * @var NodeTraverserInterface
     */
    private $traverser;

    /**
     * PhpParserNamespaceContextExtractor constructor.
     * @param Parser $parser
     * @param NamespaceContextVisitor $visitor
     */
    public function __construct(
        Parser $parser,
        NamespaceContextVisitor $visitor
    ) {
        $this->parser = $parser;
        $this->visitor = $visitor;
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new PhpParserVisitorAdapter(
            $visitor
        ));
    }

    /**
     * @param string $source
     * @return NamespaceContext
     */
    public function contextOf($source)
    {
        $this->visitor->reset();

        $statements = $this->parser->parse($source);
        $this->traverser->traverse($statements);
        return $this->visitor->context();
    }
}