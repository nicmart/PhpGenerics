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
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Transformer\BottomUpTransformer;
use NicMart\Generics\Type\Transformer\ParametricTypeTransformer;

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
     * @return SourceUnit
     */
    public function compile(
        GenericType $genericType,
        ParametrizedType $parametrizedType,
        SourceUnit $sourceUnit
    ) {
        // @todo abstract it?
        $typeTransformer =
            new BottomUpTransformer(
                new ParametricTypeTransformer(
                    $genericType,
                    $parametrizedType
                )
            )
        ;

        $nodeTransformer = $this->typeToNodeTransformer->nodeTransformer(
            $typeTransformer
        );

        $genericNodes = $this->nodeSerializer->toNodes($sourceUnit->source());
        $parametrizedNodes = $nodeTransformer->transformNodes($genericNodes);

        $parametrizedSource = $this->nodeSerializer->toSource(
            $parametrizedNodes
        );

        return new SourceUnit(
            $this->typeSerializer->serialize($parametrizedType),
            $parametrizedSource
        );
    }
}