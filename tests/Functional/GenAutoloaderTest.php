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
use NicMart\Generics\Autoloader\GenAutoloader;
use NicMart\Generics\Composer\ClassLoaderDirectoryResolver;
use NicMart\Generics\Infrastructure\Name\Context\PhpParserNamespaceContextExtractor;
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
use NicMart\Generics\Type\Source\ReflectionGenericSourceUnitLoader;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Example\Option\Option«FullName»;
use NicMart\Generics\Type\Transformer\ByCallableTypeTransformer;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Example\Func\CallableFunction1«T1·T2»;
use PhpParser\ParserFactory;

/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */
class GenAutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $genericTypeParserAndSerializer = new GenericTypeParserAndSerializer(
            new AngleQuotedGenericTypeNameParser()
        );
        
        $phpParser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);

        // This parser parses php code and annotate types
        $parser = new PostTransformParser(
            new PhpParserParser(
                $phpParser
            ),
            TraverserNodeTransformer::fromVisitors(array(
                new TypeAnnotatorVisitor(
                    $genericTypeParserAndSerializer,
                    new NamespaceContextVisitor(),
                    new PhpNameAdapter()
                )
            ))
        );

        // This serializer serializes php nodes and serialize type annotations
        $serializer = new PreTransformSerializer(
            new ChainNodeTransformer(array(
                TraverserNodeTransformer::fromVisitors(array(
                    new TypeSerializerVisitor(
                        $genericTypeParserAndSerializer,
                        new PhpNameAdapter()
                    )
                )),
                TraverserNodeTransformer::fromVisitors(array(
                    new NameSimplifierVisitor(
                        new PhpNameAdapter(),
                        new NamespaceContextVisitor()
                    )
                )),
            )),
            new PhpParserSerializer(
                new \PhpParser\PrettyPrinter\Standard()
            )
        );


        $nodeSerializer = new DefaultNodeSerializer($parser, $serializer);

        // DONE: ParametricTypeTransformer

        $autoloader = new GenAutoloader(
            
            $contextExtractor = new PhpParserNamespaceContextExtractor(
                // @todo: put our parser
                $phpParser,
                new NamespaceContextVisitor()
            ),

            $genericTypeParserAndSerializer,
        
            new DefaultParametrizedTypeLoader(

                new ComposerGenericTypeResolver(
                    $genericTypeParserAndSerializer,
                    $genericTypeParserAndSerializer,
                    new ClassLoaderDirectoryResolver(
                        ComposerAutoloaderBuilder::autoloader()
                    ),
                    $contextExtractor
                ),

                new ReflectionGenericSourceUnitLoader($genericTypeParserAndSerializer),

                new TypeBasedGenericCompiler(

                    new TypeAnnotationTypeToNodeTransformer(),
                
                    $nodeSerializer,
                
                    $genericTypeParserAndSerializer
                ),
            
                new IncludeDumpedSourceUnitEvaluation(
                    new Psr0SourceUnitDumper(__DIR__ . "/../../cache")
                )
            )
        );

        /**
         $autoloader->autoload(
            '\NicMart\Generics\Example\Option\Option«Option«FullName»»',
            __FILE__
        );
         */

        $autoloader->autoload(
            'NicMart\Generics\Example\Func\Function1«Option«FullName»·FullName»',
            __FILE__
        );

        $autoloader->autoload(
            '\NicMart\Generics\Example\Func\CallableFunction1«Option«FullName»·FullName»',
            __FILE__
        );
    }
}
