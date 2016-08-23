<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 19/08/2016, 10:29
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;

use NicMart\Generics\Infrastructure\PhpDocumentor\Adapter\PhpDocContextAdapter;
use NicMart\Generics\Infrastructure\PhpDocumentor\Type\AnnotatedType;
use NicMart\Generics\Infrastructure\PhpDocumentor\Type\RenderedType;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Type\UnionType;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Type as PhpDocType;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Object_;

/**
 * Class TypeDocBlockSerializer
 * @package NicMart\Generics\Infrastructure\PhpDocumentor
 */
class TypeDocBlockSerializer
{
    /**
     * @var DocBlock\Serializer
     */
    private $serializer;

    /**
     * @var TypeSerializer
     */
    private $typeSerializer;

    /**
     * @var TypeResolver
     */
    private $typeResolver;
    /**
     * @var PhpDocContextAdapter
     */
    private $contextAdapter;

    /**
     * TypeDocBlockSerializer constructor.
     * @param TypeResolver $typeResolver
     * @param TypeSerializer $typeSerializer
     * @param DocBlock\Serializer $serializer
     * @param PhpDocContextAdapter $contextAdapter
     */
    public function __construct(
        TypeResolver $typeResolver,
        TypeSerializer $typeSerializer,
        DocBlock\Serializer $serializer,
        PhpDocContextAdapter $contextAdapter
    ) {
        $this->serializer = $serializer;
        $this->typeSerializer = $typeSerializer;
        $this->typeResolver = $typeResolver;
        $this->contextAdapter = $contextAdapter;
    }

    /**
     * @param DocBlock $docBlock
     * @return string
     */
    public function serialize(DocBlock $docBlock)
    {
        // First, flatten the annotated types
        $docBlock = DocBlockTagFunctor::map(
            $docBlock,
            $this->typeFlattener()
        );

        $nsContext = $this->contextAdapter->fromPhpDocContext(
            $docBlock->getContext()
        );

        // This is to get around PhpDocumentor limits on types formatting
        $docBlock = DocBlockTagFunctor::map(
            $docBlock,
            $this->typeRenderer($nsContext)
        );

        return $this->serializer->getDocComment($docBlock);
    }

    /**
     * @return \Closure
     */
    private function typeFlattener()
    {
        return TagTypeFunctor::lift(function (AnnotatedType $type) {
            return $this->domainTypeToPhpDocType(
                $type->type()
            );
        });
    }

    /**
     * @param NamespaceContext $namespaceContext
     * @return \Closure
     */
    private function typeRenderer(NamespaceContext $namespaceContext)
    {
        $renderer = function (PhpDocType $type) use ($namespaceContext) {
            if (!$type instanceof Object_) {
                return $type;
            }

            return new RenderedType(
                $type,
                $namespaceContext->simplify(FullName::fromString(
                    (string) $type
                ))->toString()
            );
        };

        $recursiveRenderer = TypeFunctor::bottomUp($renderer);

        return TagTypeFunctor::lift($recursiveRenderer);
    }

    /**
     * @param Type $type
     * @return PhpDocType
     */
    private function domainTypeToPhpDocType(Type $type)
    {
        if (!$type instanceof UnionType) {
            return $this->typeResolver->resolve(
                $this->typeSerializer->serialize($type)->toString()
            );
        }

        return new Compound(array_map(
            function (Type $type) {
                return $this->domainTypeToPhpDocType($type);
            },
            $type->types()
        ));
    }
}