<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 19/08/2016, 10:29
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;

use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Type\UnionType;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Type as PhpDocType;
use phpDocumentor\Reflection\Types\Compound;

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
     * TypeDocBlockSerializer constructor.
     * @param TypeResolver $typeResolver
     * @param TypeSerializer $typeSerializer
     * @param DocBlock\Serializer $serializer
     */
    public function __construct(
        TypeResolver $typeResolver,
        TypeSerializer $typeSerializer,
        DocBlock\Serializer $serializer
    ) {
        $this->serializer = $serializer;
        $this->typeSerializer = $typeSerializer;
        $this->typeResolver = $typeResolver;
    }

    /**
     * @param DocBlock $docBlock
     * @return string
     */
    public function serialize(DocBlock $docBlock)
    {
        $docBlock = $this->serializer->getDocComment(
            DocBlockTagFunctor::map(
                $docBlock,
                TagTypeFunctor::lift(function (AnnotatedType $type) {
                    return $this->domainTypeToPhpDocType(
                        $type->type()
                    );
                })
            )
        );

        return $docBlock;
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