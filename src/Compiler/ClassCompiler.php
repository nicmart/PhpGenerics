<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Compiler;


use NicMart\Generics\Compiler\Visitor\ReplaceTypeVisitor;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

class ClassCompiler implements Compiler
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * ClassCompiler constructor.
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param $code
     * @param array $typeAssignments
     */
    public function compile($code, array $typeAssignments)
    {
        $prettyPrinter = new Standard();
        $visitor = new ReplaceTypeVisitor($typeAssignments);
        $stmts = $this->parser->parse($code);
        $nodeDumper = new NodeDumper();
        //var_dump($nodeDumper->dump($stmts));
        //var_dump($stmts);
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        //$traverser->addVisitor(new NameResolver());
        $stmts = $traverser->traverse($stmts);
        //var_dump($stmts);


        //var_dump($nodeDumper->dump($stmts));

        var_dump($prettyPrinter->prettyPrint($stmts));
    }

}