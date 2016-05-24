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
use NicMart\Generics\Name\Generic\Assignment\GenericNameAssignment;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;
use NicMart\Generics\Source\SourceUnit;
use NicMart\Generics\Source\Generic\GenericTransformerProvider;
use NicMart\Generics\Source\SourceResolver;

/**
 * Class GenericCompiler
 * @package NicMart\Generics\Source\Compiler
 */
class DefaultGenericCompiler implements GenericCompiler
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
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * GenericCompiler constructor.
     * @param SourceResolver $sourceResolver
     * @param NamespaceContextExtractor $namespaceContextExtractor
     * @param GenericTransformerProvider $genericTransformerProvider
     * @param GenericNameFactory $genericNameFactory
     */
    public function __construct(
        SourceResolver $sourceResolver,
        NamespaceContextExtractor $namespaceContextExtractor,
        GenericTransformerProvider $genericTransformerProvider,
        GenericNameFactory $genericNameFactory
    ) {
        $this->sourceResolver = $sourceResolver;
        $this->namespaceContextExtractor = $namespaceContextExtractor;
        $this->genericTransformerProvider = $genericTransformerProvider;
        $this->genericNameFactory = $genericNameFactory;
    }

    /**
     * @param FullName $genericName
     * @param FullName[] $typeParameters
     * @return SourceUnit
     */
    public function compile(
        FullName $genericName,
        array $typeParameters
    ) {
        $code = $this->sourceResolver->sourceOf($genericName);
        $context = $this->namespaceContextExtractor->contextOf($code);

        $genericNameAssignment = GenericNameAssignment::fromName(
            $genericName,
            $typeParameters,
            $context,
            $this->genericNameFactory
        );

        $transformer = $this->genericTransformerProvider->transformer(
            $genericNameAssignment
        );

        return new SourceUnit(
            $genericNameAssignment->mainAssignment()->to(),
            $transformer->transform($code)
        );
    }
}