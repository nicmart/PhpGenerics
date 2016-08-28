<?php
use NicMart\Generics\AST\Parser\PostTransformParser;
use NicMart\Generics\AST\Serializer\DefaultNodeSerializer;
use NicMart\Generics\AST\Serializer\PreTransformSerializer;
use NicMart\Generics\AST\Transformer\ChainNodeTransformer;
use NicMart\Generics\AST\Transformer\TypeAnnotationTypeToNodeTransformer;
use NicMart\Generics\AST\Visitor\NameSimplifierVisitor;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\TypeAnnotatorVisitor;
use NicMart\Generics\AST\Visitor\TypeSerializerVisitor;
use NicMart\Generics\Autoloader\ComposerAutoloaderBuilder;
use NicMart\Generics\Autoloader\ByFileGenericAutoloader;
use NicMart\Generics\Autoloader\ByFileGenericAutoloaderBuilder;
use NicMart\Generics\Composer\ClassLoaderDirectoryResolver;
use NicMart\Generics\Infrastructure\Name\Context\PhpParserNamespaceContextExtractor;
use NicMart\Generics\Infrastructure\PhpDocumentor\Adapter\PhpDocContextAdapter;
use NicMart\Generics\Infrastructure\PhpDocumentor\Serializer as PrettySerializer;
use NicMart\Generics\Infrastructure\PhpDocumentor\TypeAnnotatorDocBlockFactory;
use NicMart\Generics\Infrastructure\PhpDocumentor\TypeDocBlockSerializer;
use NicMart\Generics\Infrastructure\PhpDocumentor\Visitor\PhpDocTypeAnnotatorVisitor;
use NicMart\Generics\Infrastructure\PhpDocumentor\Visitor\PhpDocTypeSerializerVisitor;
use NicMart\Generics\Infrastructure\PhpParser\Parser\PhpParserParser;
use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Infrastructure\PhpParser\Serializer\PhpParserSerializer;
use NicMart\Generics\Infrastructure\PhpParser\Transformer\TraverserNodeTransformer;
use NicMart\Generics\Name\Generic\Parser\AngleQuotedGenericTypeNameParser;
use NicMart\Generics\Source\Dumper\Psr0SourceUnitDumper;
use NicMart\Generics\Source\Evaluation\IncludeDumpedSourceUnitEvaluation;
use NicMart\Generics\Type\Compiler\TypeBasedGenericCompiler;
use NicMart\Generics\Type\Loader\DefaultParametrizedTypeLoader;
use NicMart\Generics\Type\Parser\GenericTypeParserAndSerializer;
use NicMart\Generics\Type\Resolver\ComposerGenericTypeResolver;
use NicMart\Generics\Type\Serializer\GenericTypeSerializer;
use NicMart\Generics\Type\Source\ReflectionGenericSourceUnitLoader;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Example\PHP5\Option\Option«FullName»;
use NicMart\Generics\Type\Transformer\ByCallableTypeTransformer;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Example\PHP5\Func\CallableFunction1«T1·T2»;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use PhpParser\ParserFactory;

use NicMart\Generics\Variable\T2;
use NicMart\Generics\Variable\T1;
use NicMart\Generics\Example\PHP5\Func\Function1«T1·T2»;
use NicMart\Generics\Example\PHP5\Func\Function2«Function1«T1·T2»·T1·T2»;

/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */
class ByFileGenericAutoloaderFunctionalTest extends PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $autoloader = ByFileGenericAutoloaderBuilder::build(__DIR__ . "/../../cache");

        $autoloader->autoload(
            '\NicMart\Generics\Example\PHP5\Option\Option«FullName»',
            __FILE__
        );

         $autoloader->autoload(
            '\NicMart\Generics\Example\PHP5\Option\Option«Option«FullName»»',
            __FILE__
        );

        $autoloader->autoload(
            'NicMart\Generics\Example\PHP5\Func\Function1«Option«FullName»·FullName»',
            __FILE__
        );

        $autoloader->autoload(
            '\NicMart\Generics\Example\PHP5\Func\CallableFunction1«Option«FullName»·FullName»',
            __FILE__
        );

        $autoloader->autoload(
            '\NicMart\Generics\Example\PHP5\Option\Option«T2»',
            __FILE__
        );


        $autoloader->autoload(
            '\NicMart\Generics\Example\PHP5\Func\Function2«Function1«T1·T2»·T1·T2»',
            __FILE__
        );
    }
}
