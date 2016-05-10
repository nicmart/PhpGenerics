<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Compiler;


use NicMart\Generics\Adapter\PhpParserDocToPhpdoc;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\Infrastructure\Name\Context\PhpParserNamespaceContextExtractor;
use NicMart\Generics\Infrastructure\PhpDocumentor\Serializer;
use NicMart\Generics\Infrastructure\PhpParser\PrettyPrinter;
use NicMart\Generics\Source\Generic\DefaultGenericTransformerProvider;
use NicMart\Generics\Source\ReflectionSourceResolver;
use PhpParser\Lexer;
use PhpParser\Parser;

/**
 * Class GenericCompilerFactory
 * @package NicMart\Generics\Source\Compiler
 */
class GenericCompilerFactory
{
    /**
     * @return DefaultGenericCompiler
     */
    public static function compiler()
    {
        $parser = new Parser(new Lexer());
        $nsVisitor = new NamespaceContextVisitor();

        $sourceResolver = new ReflectionSourceResolver();
        $sourceTransformerProvider = new DefaultGenericTransformerProvider(
            $parser,
            new PrettyPrinter(),
            new PhpParserDocToPhpdoc(),
            new Serializer(),
            $nsVisitor
        );

        $namespaceExtractor = new PhpParserNamespaceContextExtractor(
            $parser,
            $nsVisitor
        );

        return new DefaultGenericCompiler(
            $sourceResolver,
            $namespaceExtractor,
            $sourceTransformerProvider
        );
    }
}