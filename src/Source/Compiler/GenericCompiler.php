<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Compiler;


use NicMart\Generics\Name\Context\NamespaceContextExtractor;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Source\FullNameWithSource;
use NicMart\Generics\Source\Generic\GenericTransformerProvider;
use NicMart\Generics\Source\SourceResolver;

/**
 * Class GenericCompiler
 * @package NicMart\Generics\Source\Compiler
 */
class GenericCompiler
{
    /**
     * @var SourceResolver
     */
    private $sourceResolver;

    /**
     * @var NamespaceContextExtractor
     */
    private $namespaceContextExtractor;

    /**
     * @var GenericTransformerProvider
     */
    private $genericTransformerProvider;

    /**
     * GenericCompiler constructor.
     * @param SourceResolver $sourceResolver
     * @param NamespaceContextExtractor $namespaceContextExtractor
     * @param GenericTransformerProvider $genericTransformerProvider
     */
    public function __construct(
        SourceResolver $sourceResolver,
        NamespaceContextExtractor $namespaceContextExtractor,
        GenericTransformerProvider $genericTransformerProvider
    ) {
        $this->sourceResolver = $sourceResolver;
        $this->namespaceContextExtractor = $namespaceContextExtractor;
        $this->genericTransformerProvider = $genericTransformerProvider;
    }

    /**
     * @param GenericName $generic
     * @param FullName[] $typeParameters
     * @return FullNameWithSource
     */
    public function compile(
        GenericName $generic,
        array $typeParameters
    ) {
        $code = $this->sourceResolver->sourceOf($generic->name());
        $context = $this->namespaceContextExtractor->contextOf($code);

        $transformer = $this->genericTransformerProvider->transformer(
            $context,
            $generic,
            $typeParameters
        );

        return new FullNameWithSource(
            $generic->apply($typeParameters),
            $transformer->transform($code)
        );
    }
}