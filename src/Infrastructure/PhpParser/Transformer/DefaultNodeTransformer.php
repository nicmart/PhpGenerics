<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser\Transformer;


use NicMart\Generics\Adapter\PhpParserDocToPhpdoc;
use NicMart\Generics\AST\PhpDoc\ReplaceTypePhpDocTransformer;
use NicMart\Generics\AST\Transformer\NodeTransformer;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\PhpDocTransformerVisitor;
use NicMart\Generics\AST\Visitor\RemoveDuplicateUsesVisitor;
use NicMart\Generics\AST\Visitor\RemoveParentTypeVisitor;
use NicMart\Generics\AST\Visitor\TypeDefinitionTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeUsageTransformerVisitor;
use NicMart\Generics\AST\Transformer\ChainNodeTransformer;
use NicMart\Generics\Name\Assignment\SimpleNameAssignmentContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Assignment\GenericNameAssignment;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;
use NicMart\Generics\Name\Transformer\ByFullNameNameTransformer;
use NicMart\Generics\Name\Transformer\ChainNameTransformer;
use NicMart\Generics\Name\Transformer\GenericNameTransformer;
use NicMart\Generics\Name\Transformer\LazyNameTransformer;
use NicMart\Generics\Name\Transformer\NameTransformer;
use NicMart\Generics\Name\Transformer\SimpleNameTransformer;
use NicMart\Generics\Name\Transformer\SimplifierNameTransformer;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\Node;

/**
 * Class DefaultNodeTransformer
 * @package NicMart\Generics\Infrastructure\PhpParser\Transformer
 */
class DefaultNodeTransformer implements NodeTransformer
{
    /**
     * @var PhpParserDocToPhpdoc
     */
    private $phpParserDocToPhpdoc;

    /**
     * @var Serializer
     */
    private $phpDocSerializer;

    /**
     * @var NamespaceContextVisitor
     */
    private $namespaceContextVisitor;

    /**
     * @var GenericNameAssignment
     */
    private $genericNameAssignment;

    /**
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * DefaultNodeTransformer constructor.
     * @param PhpParserDocToPhpdoc $phpParserDocToPhpdoc
     * @param Serializer $phpDocSerializer
     * @param NamespaceContextVisitor $namespaceContextVisitor
     * @param GenericNameFactory $genericNameFactory
     * @param GenericNameAssignment $genericNameAssignment
     */
    public function __construct(
        PhpParserDocToPhpdoc $phpParserDocToPhpdoc,
        Serializer $phpDocSerializer,
        NamespaceContextVisitor $namespaceContextVisitor,
        GenericNameFactory $genericNameFactory,
        GenericNameAssignment $genericNameAssignment
    ) {
        $this->phpParserDocToPhpdoc = $phpParserDocToPhpdoc;
        $this->phpDocSerializer = $phpDocSerializer;
        $this->namespaceContextVisitor = $namespaceContextVisitor;
        $this->genericNameAssignment = $genericNameAssignment;
        $this->genericNameFactory = $genericNameFactory;
    }

    /**
     * @param array $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        $transformer = new ChainNodeTransformer(array(
            $this->typeReplacer(),
            $this->nameSimplifier()
        ));

        return $transformer->transformNodes($nodes);
    }

    /**
     * @return TraverserNodeTransformer
     */
    private function typeReplacer()
    {
        // Assignment for type definition replacement
        $typeDefAssignments = new SimpleNameAssignmentContext(array(
            $this->genericNameAssignment->mainSimpleNameAssignment()
        ));

        // Transform type vars in concrete types
        $simpleTypeUsageTransformer = new ByFullNameNameTransformer(
            $this->genericNameAssignment->typeAssignments()
        );

        $factory = $this->genericNameFactory;


        // Recursive name transformer!
        $typeUsageTransformer = new LazyNameTransformer(function (
            NameTransformer $chain
        ) use ($simpleTypeUsageTransformer, $factory) {
            return new ChainNameTransformer(array(
                new GenericNameTransformer(
                    $chain,
                    $factory
                ),
                $simpleTypeUsageTransformer
            ));
        });

        return $this->typeTransformerVisitor(
            $typeUsageTransformer,
            $typeDefAssignments
        );

    }

    private function typeTransformerVisitor(
        NameTransformer $typeUsageTransformer,
        SimpleNameTransformer $typeDefTransformer
    ) {
        return TraverserNodeTransformer::fromVisitors(array(
            // Set the namespace
            $this->namespaceContextVisitor,
            // Transforms type usages
            new TypeUsageTransformerVisitor($typeUsageTransformer),
            // Transforms type definitions (class/interfaces)
            new TypeDefinitionTransformerVisitor($typeDefTransformer),
            // Transforms phpdocs
            new PhpDocTransformerVisitor(
                new ReplaceTypePhpDocTransformer(
                    $typeUsageTransformer,
                    $this->phpParserDocToPhpdoc,
                    $this->phpDocSerializer
                )
            ),
            // Remove Generic marker interface
            new RemoveParentTypeVisitor(array(
                FullName::fromString('\NicMart\Generics\Generic')
            )),
        ));
    }

    /**
     * @return TraverserNodeTransformer
     */
    private function nameSimplifier()
    {
        $uses = new Uses();
        foreach ($this->genericNameAssignment->typeArguments() as $typeArgument) {
            if (!$typeArgument->isNative()) {
                $uses = $uses->withUse(new Use_($typeArgument));
            }
        }

        $simplifyNameTransformer = new SimplifierNameTransformer($uses);

        return $this->nameSimplifierTransformer($simplifyNameTransformer);
    }

    /**
     * @param $simplifyNameTransformer
     * @return TraverserNodeTransformer
     */
    private function nameSimplifierTransformer(
        NameTransformer $simplifyNameTransformer
    ) {
        return TraverserNodeTransformer::fromVisitors(array(
            //new AddUsesVisitor($uses),
            $this->namespaceContextVisitor,
            // Deduplicate usage statements created by the previous stage
            new RemoveDuplicateUsesVisitor(),
            new TypeUsageTransformerVisitor(
                $simplifyNameTransformer
            ),
            new PhpDocTransformerVisitor(
                new ReplaceTypePhpDocTransformer(
                    $simplifyNameTransformer,
                    $this->phpParserDocToPhpdoc,
                    $this->phpDocSerializer
                )
            )
        ));
    }
}