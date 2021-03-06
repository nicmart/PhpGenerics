<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Loader;


use NicMart\Generics\Source\Evaluation\SourceUnitEvaluation;
use NicMart\Generics\Type\Compiler\CompilationResult;
use NicMart\Generics\Type\Compiler\GenericCompiler;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Resolver\GenericTypeResolver;
use NicMart\Generics\Type\Source\GenericSourceUnitLoader;

/**
 * Class DefaultParametrizedTypeLoader
 * @package NicMart\Generics\Type\Loader
 */
final class DefaultParametrizedTypeLoader implements ParametrizedTypeLoader
{
    /**
     * @var GenericTypeResolver
     */
    private $genericTypeResolver;
    /**
     * @var GenericSourceUnitLoader
     */
    private $sourceUnitLoader;
    /**
     * @var GenericCompiler
     */
    private $compiler;
    /**
     * @var SourceUnitEvaluation
     */
    private $sourceUnitEvaluation;

    /**
     * DefaultParametrizedTypeLoader constructor.
     * @param GenericTypeResolver $genericTypeResolver
     * @param GenericSourceUnitLoader $sourceUnitLoader
     * @param GenericCompiler $compiler
     * @param SourceUnitEvaluation $sourceUnitEvaluation
     */
    public function __construct(
        GenericTypeResolver $genericTypeResolver,
        GenericSourceUnitLoader $sourceUnitLoader,
        GenericCompiler $compiler,
        SourceUnitEvaluation $sourceUnitEvaluation
    ) {
        $this->genericTypeResolver = $genericTypeResolver;
        $this->sourceUnitLoader = $sourceUnitLoader;
        $this->compiler = $compiler;
        $this->sourceUnitEvaluation = $sourceUnitEvaluation;
    }

    /**
     * @param ParametrizedType $parametrizedType
     * 
     * @return CompilationResult|null
     */
    public function load(ParametrizedType $parametrizedType)
    {
        // Note: This autoloads the generic type
        $genericType = $this->genericTypeResolver->toGenericType(
            $parametrizedType
        );

        if ($genericType->toParametrizedType() == $parametrizedType) {
            return null;
        }

        $genericSourceUnit = $this->sourceUnitLoader->loadSource($genericType);

        $compilationResult = $this->compiler->compile(
            $genericType,
            $parametrizedType,
            $genericSourceUnit
        );

        $this->sourceUnitEvaluation->evaluate($compilationResult->sourceUnit());

        return $compilationResult;
    }
}