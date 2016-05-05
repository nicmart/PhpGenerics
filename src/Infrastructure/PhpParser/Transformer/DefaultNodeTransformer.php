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
use NicMart\Generics\AST\Visitor\AddUsesVisitor;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\PhpDocTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeDefinitionTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeUsageTransformerVisitor;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\AngleQuotedGenericNameFactory;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\Transformer\AutoloadNameTransformer;
use NicMart\Generics\Name\Transformer\ByFullNameNameTransformer;
use NicMart\Generics\Name\Transformer\ChainNameTransformer;
use NicMart\Generics\Name\Transformer\GenericNameTransformer;
use NicMart\Generics\Name\Transformer\ListenerNameTransformer;
use NicMart\Generics\Name\Transformer\NameQualifier;
use NicMart\Generics\Name\Transformer\SimplifierNameTransformer;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class DefaultNodeTransformer
 * @package NicMart\Generics\Infrastructure\PhpParser\Transformer
 */
class DefaultNodeTransformer implements NodeTransformer
{
    /**
     * @var NameQualifier
     */
    private $qualifier;

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
     * DefaultNodeTransformer constructor.
     * @param PhpParserDocToPhpdoc $phpParserDocToPhpdoc
     * @param Serializer $phpDocSerializer
     * @param NamespaceContextVisitor $namespaceContextVisitor
     * @param NameQualifier $qualifier
     * @param GenericName $generic
     * @param array $typeParameters
     */
    public function __construct(
        PhpParserDocToPhpdoc $phpParserDocToPhpdoc,
        Serializer $phpDocSerializer,
        NamespaceContextVisitor $namespaceContextVisitor,
        NameQualifier $qualifier,
        GenericName $generic,
        array $typeParameters
    ) {
        $this->qualifier = $qualifier;
        $this->generic = $generic;
        $this->typeParameters = $typeParameters;
        $this->phpParserDocToPhpdoc = $phpParserDocToPhpdoc;
        $this->phpDocSerializer = $phpDocSerializer;
        $this->namespaceContextVisitor = $namespaceContextVisitor;
    }

    /**
     * @param array $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        $uses = new Uses();

        $nodes = $this->typeReplacer($uses)->transformNodes($nodes);
        
        return $this->nameSimplifier($uses)->transformNodes($nodes);
    }

    /**
     * @param Uses $uses
     *
     * @return TraverserNodeTransformer
     */
    private function typeReplacer(Uses &$uses)
    {
        $typeUsageAssignment = $this->generic->assignments(
            $this->typeParameters,
            $this->qualifier
        );

        $typeDefAssignments = $this->generic->simpleAssignments(
            $this->typeParameters
        );

        $transformationsCollector = function (
            Name $from,
            Name $to,
            NamespaceContext $context
        ) use (&$uses) {
            // @todo refactor
            if (strpos($from->toString(), "«") !== false) {
                $uses = $uses
                    ->withUse(new Use_($context->qualify($from)))
                    ->withUse(new Use_($context->qualify($to)))
                ;
            }
        };

        $simpleTypeUsageTransformer = new ByFullNameNameTransformer($typeUsageAssignment);

        $typeUsageTransformer = new ListenerNameTransformer(
            ChainNameTransformer::fromNameTransformerFactory(
                function (ChainNameTransformer $chain) use ($simpleTypeUsageTransformer) {
                    return array(
                        new GenericNameTransformer(
                            $chain,
                            new AngleQuotedGenericNameFactory()
                        ),
                        $simpleTypeUsageTransformer
                    );
                }
            ),
            $transformationsCollector
        );

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
            )
        ));
    }

    /**
     * @param Uses $uses
     * @return TraverserNodeTransformer
     */
    private function nameSimplifier(Uses $uses)
    {
        foreach ($this->typeParameters as $typeParameter) {
            if (!$typeParameter->isNative()) {
                $uses = $uses->withUse(new Use_($typeParameter));
            }
        }

        return TraverserNodeTransformer::fromVisitors(array(
            new AddUsesVisitor($uses),
            $this->namespaceContextVisitor,
            new TypeUsageTransformerVisitor(
                new SimplifierNameTransformer($uses)
            ),
            new PhpDocTransformerVisitor(
                new ReplaceTypePhpDocTransformer(
                    new SimplifierNameTransformer($uses),
                    $this->phpParserDocToPhpdoc,
                    $this->phpDocSerializer
                )
            )
        ));
    }
}