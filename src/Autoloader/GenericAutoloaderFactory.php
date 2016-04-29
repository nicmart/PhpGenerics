<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Autoloader;

use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\Infrastructure\Name\Context\PhpParserNamespaceContextExtractor;
use NicMart\Generics\Infrastructure\Source\CallerFilenameResolver;
use NicMart\Generics\Source\Compiler\GenericCompilerFactory;
use NicMart\Generics\Source\Evaluation\Psr0SourceUnitEvaluation;
use PhpParser\Lexer;
use PhpParser\Parser;

class GenericAutoloaderFactory
{
    public static function registerAutoloader($baseDir)
    {
        $genericAutoloader = new GenericAutoloader(
            GenericCompilerFactory::compiler(),
            new Psr0SourceUnitEvaluation($baseDir),
            new CallerFilenameResolver(),
            new PhpParserNamespaceContextExtractor(
                new Parser(new Lexer()),
                new NamespaceContextVisitor()
            )
        );

        spl_autoload_register($genericAutoloader);
    }
}