<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 19/08/2016, 12:36
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;


use NicMart\Generics\Infrastructure\PhpDocumentor\Type\AnnotatedType;
use NicMart\Generics\Type\Transformer\TypeTransformer;
use phpDocumentor\Reflection\DocBlock;

/**
 * Class TypeDocBlockTransformer
 * @package NicMart\Generics\Infrastructure\PhpDocumentor
 */
final class TypeDocBlockTransformer
{
    /**
     * @var TypeTransformer
     */
    private $typeTransformer;

    /**
     * TypeDocBlockTransformer constructor.
     * @param TypeTransformer $typeTransformer
     */
    public function __construct(TypeTransformer $typeTransformer)
    {
        $this->typeTransformer = $typeTransformer;
    }

    /**
     * @param DocBlock $docBlock
     * @return DocBlock
     */
    public function transform(DocBlock $docBlock)
    {
        return DocBlockTagFunctor::map(
            $docBlock,
            TagTypeFunctor::lift(
                function (AnnotatedType $annotatedType) {
                    return $annotatedType->withType(
                        $this->typeTransformer->transform(
                            $annotatedType->type()
                        )
                    );
                }
            )
        );
    }
}