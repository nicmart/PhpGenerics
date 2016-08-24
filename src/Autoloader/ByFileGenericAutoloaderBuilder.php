<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 24/08/2016, 15:58
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Autoloader;


use NicMart\Generics\AST\Parser\PostTransformParser;
use NicMart\Generics\AST\Serializer\DefaultNodeSerializer;
use NicMart\Generics\AST\Serializer\PreTransformSerializer;
use NicMart\Generics\AST\Transformer\ChainNodeTransformer;
use NicMart\Generics\AST\Transformer\TypeAnnotationTypeToNodeTransformer;
use NicMart\Generics\AST\Visitor\NameSimplifierVisitor;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\RemoveDuplicateUsesVisitor;
use NicMart\Generics\AST\Visitor\TypeAnnotatorVisitor;
use NicMart\Generics\AST\Visitor\TypeSerializerVisitor;
use NicMart\Generics\Composer\ClassLoaderDirectoryResolver;
use NicMart\Generics\Infrastructure\Name\Context\PhpParserNamespaceContextExtractor;
use NicMart\Generics\Infrastructure\PhpDocumentor\Adapter\PhpDocContextAdapter;
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
use NicMart\Generics\Infrastructure\PhpDocumentor\Serializer as PrettySerializer;
use NicMart\Generics\Type\Resolver\ComposerGenericTypeResolver;
use NicMart\Generics\Type\Source\ReflectionGenericSourceUnitLoader;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use PhpParser\ParserFactory;

/**
 * Class GenAutoloaderBuilder
 * @package NicMart\Generics\Autoloader
 */
class ByFileGenericAutoloaderBuilder
{
    /**
     * @param string $cacheFolder
     * @return ByFileGenericAutoloader
     */
    public static function build($cacheFolder)
    {
        $genericTypeParserAndSerializer = new GenericTypeParserAndSerializer(
            new AngleQuotedGenericTypeNameParser()
        );

        $phpParser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
        $phpDocContextAdapter = new PhpDocContextAdapter();

        // TODO: before serializing we should put the new namespace
        // into the DocBlock, otherwise it will be serialized with the old context

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
                ),
                new PhpDocTypeAnnotatorVisitor(
                    TypeAnnotatorDocBlockFactory::createInstance(
                        $genericTypeParserAndSerializer
                    ),
                    $phpDocContextAdapter
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
                    ),
                )),
                TraverserNodeTransformer::fromVisitors(array(
                    new NameSimplifierVisitor(
                        new PhpNameAdapter(),
                        new NamespaceContextVisitor()
                    ),
                    new PhpDocTypeSerializerVisitor(
                        new TypeDocBlockSerializer(
                            new TypeResolver(new FqsenResolver()),
                            $genericTypeParserAndSerializer,
                            new PrettySerializer(),
                            $phpDocContextAdapter
                        ),
                        $phpDocContextAdapter
                    ),
                )),
                // This breaks
                TraverserNodeTransformer::fromVisitors(array(
                    new NamespaceContextVisitor(),
                    new RemoveDuplicateUsesVisitor()
                ))
            )),
            new PhpParserSerializer(
                new \PhpParser\PrettyPrinter\Standard()
            )
        );

        $nodeSerializer = new DefaultNodeSerializer($parser, $serializer);

        return new ByFileGenericAutoloader(

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
                    new Psr0SourceUnitDumper($cacheFolder)
                )
            )
        );
    }
}