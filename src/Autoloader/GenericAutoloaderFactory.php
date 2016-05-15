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
use ReflectionClass;
use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 * Class GenericAutoloaderFactory
 * @package NicMart\Generics\Autoloader
 */
class GenericAutoloaderFactory
{
    /**
     * @param $baseDir
     */
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
                static::composerClassLoader()
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

    /**
     * @return mixed
     */
    private static function composerClassLoader()
    {
        $baseDir = dirname(dirname(__DIR__));

        if (static::isComposerDependency($baseDir)) {
            return include dirname(dirname($baseDir)) . DIRECTORY_SEPARATOR . "autoload.php";
        }

        $path = $baseDir . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

        if (file_exists($path)) {
            return include $path;
        }

        throw new RuntimeException("Unable to find composer autoload.php file");
    }

    /**
     * @param $baseDir
     * @return bool
     */
    private static function isComposerDependency($baseDir)
    {
        $nicmartVendorFolder = dirname($baseDir);
        $vendorFolder = dirname($nicmartVendorFolder);

        return
            basename($baseDir) == "php-generics"
            && basename($nicmartVendorFolder) == "nicmart"
            && file_exists($vendorFolder . DIRECTORY_SEPARATOR . "autoload.php")
        ;
    }
}