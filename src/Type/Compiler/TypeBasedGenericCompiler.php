<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Compiler;


use NicMart\Generics\AST\Serializer\DefaultNodeSerializer;
use NicMart\Generics\AST\Transformer\TypeToNodeTransformer;
use NicMart\Generics\Source\SourceUnit;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Transformer\BottomUpTransformer;
use NicMart\Generics\Type\Transformer\ByCallableTypeTransformer;
use NicMart\Generics\Type\Transformer\ChainTypeTransformer;
use NicMart\Generics\Type\Transformer\ListenerTypeTransformer;
use NicMart\Generics\Type\Transformer\ParametricTypeTransformer;
use NicMart\Generics\Type\Transformer\TopDownTransformer;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Type\UnionType;

/**
 * Class TypeBasedGenericCompiler
 * @package NicMart\Generics\Type\Compiler
 */
class TypeBasedGenericCompiler implements GenericCompiler
{
    /**
     * @var TypeToNodeTransformer
     */
    private $typeToNodeTransformer;

    /**
     * @var DefaultNodeSerializer
     */
    private $nodeSerializer;

    /**
     * @var TypeSerializer
     */
    private $typeSerializer;

    /**
     * TypeBasedGenericCompiler constructor.
     * @param TypeToNodeTransformer $typeToNodeTransformer
     * @param DefaultNodeSerializer $nodeSerializer
     * @param TypeSerializer $typeSerializer
     */
    public function __construct(
        TypeToNodeTransformer $typeToNodeTransformer,
        DefaultNodeSerializer $nodeSerializer,
        TypeSerializer $typeSerializer
    ) {
        $this->typeToNodeTransformer = $typeToNodeTransformer;
        $this->nodeSerializer = $nodeSerializer;
        $this->typeSerializer = $typeSerializer;
    }

    /**
     * @param GenericType $genericType
     * @param ParametrizedType $parametrizedType
     * @param SourceUnit $sourceUnit
     * @return CompilationResult
     */
    public function compile(
        GenericType $genericType,
        ParametrizedType $parametrizedType,
        SourceUnit $sourceUnit
    ) {
        $nodeTransformer = $this->typeToNodeTransformer->nodeTransformer(
            $this->transformer($genericType, $parametrizedType, $transformedTypes)
        );

        $genericNodes = $this->nodeSerializer->toNodes($sourceUnit->source());
        $parametrizedNodes = $nodeTransformer->transformNodes($genericNodes);

        $parametrizedSource = $this->nodeSerializer->toSource(
            $parametrizedNodes
        );

        return new CompilationResult(
            new SourceUnit(
                $this->typeSerializer->serialize($parametrizedType),
                $parametrizedSource
            ),
            $this->typeSerializer,
            $transformedTypes
        );
    }

    // @todo abstract it?
    private function transformer(
        GenericType $genericType,
        ParametrizedType $parametrizedType,
        &$transformedTypes
    ) {
        // First, transforms all generic to parametrized types, top down
        $genericToParametrized = new TopDownTransformer(
            new ByCallableTypeTransformer(function (Type $type) {
                if (!$type instanceof GenericType) {
                    return $type;
                }

                return new ParametrizedType(
                    $type->name(),
                    $type->parameters()
                );
            })
        );

        // Then, transform the types, bottom up
        $typeTransformer = new BottomUpTransformer(
            new ParametricTypeTransformer(
                $genericType,
                $parametrizedType
            )
        );

        $transformedTypes = [];
        return new ListenerTypeTransformer(
            new ChainTypeTransformer([
                $genericToParametrized,
                $typeTransformer
            ]),
            $this->typeCollectorListener(
                $transformedTypes
            )
        );
    }

    /**
     * This collects the types involved in the transformations
     *
     * @param $types
     * @return \Closure
     */
    private function typeCollectorListener(&$types)
    {
        return function (Type $from, Type $to) use (&$types) {
            if ($to instanceof UnionType
                || $to instanceof PrimitiveType
            ) {
                return;
            }
            $types[] = $from;
            $types[] = $to;
        };
    }
}