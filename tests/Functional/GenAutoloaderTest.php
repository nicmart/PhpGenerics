<?php
use NicMart\Generics\AST\Parser\PostTransformParser;
use NicMart\Generics\AST\Serializer\DefaultNodeSerializer;
use NicMart\Generics\AST\Serializer\PreTransformSerializer;
use NicMart\Generics\AST\Transformer\TypeAnnotationTypeToNodeTransformer;
use NicMart\Generics\AST\Visitor\Name\TypeAnnotatorNameVisitor;
use NicMart\Generics\AST\Visitor\Name\TypeSerializerNameVisitor;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\TypeNameVisitor;
use NicMart\Generics\Autoloader\GenAutoloader;
use NicMart\Generics\Infrastructure\Name\Context\PhpParserNamespaceContextExtractor;
use NicMart\Generics\Infrastructure\PhpParser\Parser\PhpParserParser;
use NicMart\Generics\Infrastructure\PhpParser\PhpParserSerializer;
use NicMart\Generics\Infrastructure\PhpParser\Transformer\TraverserNodeTransformer;
use NicMart\Generics\Name\Generic\Parser\AngleQuotedGenericTypeNameParser;
use NicMart\Generics\Source\Dumper\Psr0SourceUnitDumper;
use NicMart\Generics\Source\Evaluation\IncludeDumpedSourceUnitEvaluation;
use NicMart\Generics\Type\Compiler\GenericCompiler;
use NicMart\Generics\Type\Compiler\TypeBasedGenericCompiler;
use NicMart\Generics\Type\Loader\DefaultParametrizedTypeLoader;
use NicMart\Generics\Type\Parser\GenericTypeParser;
use NicMart\Generics\Type\Resolver\GenericTypeResolver;
use NicMart\Generics\Type\Serializer\GenericTypeSerializer;
use NicMart\Generics\Type\Source\GenericSourceUnitLoader;

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
        $genericTypeParser = new GenericTypeParser(
            new AngleQuotedGenericTypeNameParser()
        );

        // This parser parses php code and annotate types
        $parser = new PostTransformParser(
            new PhpParserParser(
                $phpParser = new \PhpParser\Parser(new \PhpParser\Lexer())
            ),
            TraverserNodeTransformer::fromVisitors(array(
                new NamespaceContextVisitor(),
                new TypeNameVisitor(
                    new TypeAnnotatorNameVisitor(
                        $genericTypeParser
                    )
                )
            ))
        );

        // This serializer serializes php nodes and serialize type annotations
        $serializer = new PreTransformSerializer(
            TraverserNodeTransformer::fromVisitors(array(
                new TypeNameVisitor(
                    new TypeSerializerNameVisitor(
                        // TO IMPLEMENT
                        new GenericTypeSerializer()
                    )
                )
            )),
            new PhpParserSerializer(
                new \PhpParser\PrettyPrinter\Standard()
            )
        );


        $nodeSerializer = new DefaultNodeSerializer($parser, $serializer);

        // DONE: ParametricTypeTransformer

        $autoloader = new GenAutoloader(
            
            new PhpParserNamespaceContextExtractor(
                // @todo: put our parser
                $phpParser,
                new NamespaceContextVisitor()
            ),

            $genericTypeParser,
        
            new DefaultParametrizedTypeLoader(

                /** @var GenericTypeResolver TO IMPLEMENT */
                $genericTypeResolver = null,

                /** @var GenericSourceUnitLoader TO IMPLEMENT */
                $sourceUnitLoader = null,

                new TypeBasedGenericCompiler(

                    new TypeAnnotationTypeToNodeTransformer(),
                
                    $nodeSerializer,
                
                    // To IMPLEMENT
                    new GenericTypeSerializer()
                ),
            
                new IncludeDumpedSourceUnitEvaluation(
                    new Psr0SourceUnitDumper(__DIR__)
                )
            )
        );

        $autoloader->autoload("Foo\\Bar", __FILE__);
    }
}
