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
use NicMart\Generics\Composer\ClassLoaderDirectoryResolver;
use NicMart\Generics\Composer\ComposerGenericNameResolver;
use NicMart\Generics\Infrastructure\Name\Context\PhpParserNamespaceContextExtractor;
use NicMart\Generics\Infrastructure\Source\CallerFilenameResolver;
use NicMart\Generics\Name\Generic\CallerContextGenericNameResolver;
use NicMart\Generics\Name\Generic\Factory\AngleQuotedGenericNameFactory;
use NicMart\Generics\Source\Compiler\GenericCompilerFactory;
use NicMart\Generics\Source\Dumper\Psr0SourceUnitDumper;
use NicMart\Generics\Source\Evaluation\IncludeDumpedSourceUnitEvaluation;
use PhpParser\Lexer;
use PhpParser\Parser;

class GenericAutoloaderFactory
{
    public static function registerAutoloader($baseDir)
    {
        $srcDir = dirname(__DIR__);
        $filenameResolver = new CallerFilenameResolver(array(
            $srcDir . "/Name/Generic/CallerContextGenericNameResolver.php",
            $srcDir . "/Autoloader/GenericAutoloader.php",
        ));
        $genericFactory = new AngleQuotedGenericNameFactory();
        $nsExtractor = new PhpParserNamespaceContextExtractor(
            new Parser(new Lexer()),
            new NamespaceContextVisitor()
        );
        $resolver = new CallerContextGenericNameResolver(
            $genericFactory,
            $filenameResolver,
            $nsExtractor
        );

        $resolver = new ComposerGenericNameResolver(
            $genericFactory,
            new ClassLoaderDirectoryResolver(
                include $srcDir . "/../vendor/autoload.php"
            )
        );

        $genericAutoloader = new GenericAutoloader(
            GenericCompilerFactory::compiler(),
            new IncludeDumpedSourceUnitEvaluation(
                new Psr0SourceUnitDumper($baseDir)
            ),
            $filenameResolver,
            $nsExtractor,
            $resolver,
            $genericFactory
        );

        spl_autoload_register($genericAutoloader);
    }
}