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
use NicMart\Generics\Name\Context\NamespaceContextResolver;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;

class CallerNamespaceContextResolver implements NamespaceContextResolver
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
     * CallerNamespaceContextResolver constructor.
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

    public function resolve()
    {
        $this->visitor->reset();

        $filename = $this->filename();

        var_dump($filename);

        $statements = $this->parser->parse(file_get_contents($filename));
        $this->traverser->traverse($statements);

        return $this->visitor->context();
    }

    private function filename()
    {
        $trace = debug_backtrace();

        foreach ($trace as $entry) {
            if (isset($entry["file"]) && $entry["file"] != __FILE__) {
                return $entry["file"];
            }
        }

        throw new \UnderflowException("Found no filename in the backtrace");
    }
}