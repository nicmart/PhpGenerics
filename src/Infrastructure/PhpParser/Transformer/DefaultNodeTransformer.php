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
use NicMart\Generics\AST\Visitor\AddUsesVisitor;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\PhpDocTransformerVisitor;
use NicMart\Generics\AST\Visitor\RemoveParentTypeVisitor;
use NicMart\Generics\AST\Visitor\TypeDefinitionTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeUsageTransformerVisitor;
use NicMart\Generics\Name\Assignment\SimpleNameAssignment;
use NicMart\Generics\Name\Assignment\SimpleNameAssignmentContext;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\Transformer\ByFullNameNameTransformer;
use NicMart\Generics\Name\Transformer\ChainNameTransformer;
use NicMart\Generics\Name\Transformer\GenericNameTransformer;
use NicMart\Generics\Name\Transformer\ListenerNameTransformer;
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
     * @var GenericName
     */
    private $generic;

    /**
     * @var FullName[]
     */
    private $typeParameters;

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
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * DefaultNodeTransformer constructor.
     * @param PhpParserDocToPhpdoc $phpParserDocToPhpdoc
     * @param Serializer $phpDocSerializer
     * @param NamespaceContextVisitor $namespaceContextVisitor
     * @param GenericNameFactory $genericNameFactory
     * @param GenericName $generic
     * @param array $typeParameters
     */
    public function __construct(
        PhpParserDocToPhpdoc $phpParserDocToPhpdoc,
        Serializer $phpDocSerializer,
        NamespaceContextVisitor $namespaceContextVisitor,
        GenericNameFactory $genericNameFactory,
        GenericName $generic,
        array $typeParameters
    ) {
        $this->generic = $generic;
        $this->typeParameters = $typeParameters;
        $this->phpParserDocToPhpdoc = $phpParserDocToPhpdoc;
        $this->phpDocSerializer = $phpDocSerializer;
        $this->namespaceContextVisitor = $namespaceContextVisitor;
        $this->genericNameFactory = $genericNameFactory;
    }

    /**
     * @param array $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        $nodes = $this->typeReplacer()->transformNodes($nodes);

        return $this->nameSimplifier()->transformNodes($nodes);
    }

    /**
     * @return TraverserNodeTransformer
     */
    private function typeReplacer()
    {
        $factory = $this->genericNameFactory;

        // Assignments for type usages: from generict type param to concrete types
        $typeUsageAssignment = $this->generic->assignments(
            $this->typeParameters
        );

        $appliedGeneric = $this->generic->apply($this->typeParameters);

        // Assignment for type definition replacement
        $typeDefAssignments = new SimpleNameAssignmentContext(array(
            new SimpleNameAssignment(
                $this->genericNameFactory->fromGeneric($this->generic)->last(),
                $this->genericNameFactory->fromGeneric($appliedGeneric)->last()
            )
        ));

        // Transform type vars in concrete types
        $simpleTypeUsageTransformer = new ByFullNameNameTransformer(
            $typeUsageAssignment
        );

        // Recursive type transformer, transform type vars in concrete types,
        // and transform generic arguments recursively
        $typeUsageTransformer =
            ChainNameTransformer::fromNameTransformerFactory(
                function (
                    ChainNameTransformer $chain
                ) use ($simpleTypeUsageTransformer, $factory) {
                    return array(
                        new GenericNameTransformer(
                            $chain,
                            $factory
                        ),
                        $simpleTypeUsageTransformer
                    );
                }
            )
        ;

        // Define the visitor chain
        return TraverserNodeTransformer::fromVisitors(array(
            $this->namespaceContextVisitor,
            new TypeUsageTransformerVisitor($typeUsageTransformer),
            new TypeDefinitionTransformerVisitor($typeDefAssignments),
            new PhpDocTransformerVisitor(
                new ReplaceTypePhpDocTransformer(
                    $typeUsageTransformer,
                    $this->phpParserDocToPhpdoc,
                    $this->phpDocSerializer
                )
            ),
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
        foreach ($this->typeParameters as $typeParameter) {
            if (!$typeParameter->isNative()) {
                $uses = $uses->withUse(new Use_($typeParameter));
            }
        }

        $simplifyNameTransformer = new SimplifierNameTransformer($uses);

        return TraverserNodeTransformer::fromVisitors(array(
            new AddUsesVisitor($uses),
            $this->namespaceContextVisitor,
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