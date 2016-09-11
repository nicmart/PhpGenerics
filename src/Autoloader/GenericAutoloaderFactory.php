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
        spl_autoload_register(new GenericAutoloader(
            ByFileGenericAutoloaderBuilder::build($baseDir),
            new CallerFilenameResolver(array(
            ))
        ));
    }
}