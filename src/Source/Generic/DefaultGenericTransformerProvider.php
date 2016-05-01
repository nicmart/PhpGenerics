<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Generic;


use NicMart\Generics\Adapter\PhpParserDocToPhpdoc;
use NicMart\Generics\Adapter\PhpParserVisitorAdapter;
use NicMart\Generics\AST\PhpDoc\ReplaceTypePhpDocTransformer;
use NicMart\Generics\AST\Visitor\AddUsesVisitor;
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\AST\Visitor\PhpDocTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeDefinitionTransformerVisitor;
use NicMart\Generics\AST\Visitor\TypeUsageTransformerVisitor;
use NicMart\Generics\Infrastructure\Source\Transformer\PhpParserSourceTransformer;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\AngleQuotedGenericNameFactory;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Name\Transformer\ByFullNameNameTransformer;
use NicMart\Generics\Name\Transformer\ChainNameTransformer;
use NicMart\Generics\Name\Transformer\GenericNameTransformer;
use NicMart\Generics\Name\Transformer\NameQualifier;
use NicMart\Generics\Name\Transformer\SimplifierNameTransformer;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class DefaultGenericTransformerProvider
 * @package NicMart\Generics\Source\Generic
 */
class DefaultGenericTransformerProvider implements GenericTransformerProvider
{
    /**
     * @var Parser
     */
    private $phpParser;
    /**
     * @var Standard
     */
    private $phpPrettyPrinter;
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
     * DefaultGenericTransformerProvider constructor.
     * @param Parser $phpParser
     * @param Standard $phpPrettyPrinter
     * @param PhpParserDocToPhpdoc $phpParserDocToPhpdoc
     * @param Serializer $phpDocSerializer
     * @param NamespaceContextVisitor $namespaceContextVisitor
     */
    public function __construct(
        Parser $phpParser,
        Standard $phpPrettyPrinter,
        PhpParserDocToPhpdoc $phpParserDocToPhpdoc,
        Serializer $phpDocSerializer,
        NamespaceContextVisitor $namespaceContextVisitor
    ) {
        $this->phpParser = $phpParser;
        $this->phpPrettyPrinter = $phpPrettyPrinter;
        $this->phpParserDocToPhpdoc = $phpParserDocToPhpdoc;
        $this->phpDocSerializer = $phpDocSerializer;
        $this->namespaceContextVisitor = $namespaceContextVisitor;
    }

    /**
     * @param NameQualifier $qualifier
     * @param GenericName $generic
     * @param FullName[] $typeParameters
     * @return PhpParserSourceTransformer
     */
    public function transformer(
        NameQualifier $qualifier,
        GenericName $generic,
        array $typeParameters
    ) {
        $typeUsageAssignment = $generic->assignments(
            $typeParameters,
            $qualifier
        );

        $typeDefAssignments = $generic->simpleAssignments($typeParameters);

        $typeUsageTransformer = new ByFullNameNameTransformer($typeUsageAssignment);
        $typeUsageTransformer = new ChainNameTransformer(array(
            $typeUsageTransformer,
            new GenericNameTransformer($typeUsageAssignment, new AngleQuotedGenericNameFactory())
        ));

        $traverser1 = new NodeTraverser();

        $traverser1->addVisitor(
            new PhpParserVisitorAdapter($this->namespaceContextVisitor)
        );

        $traverser1->addVisitor(
            new PhpParserVisitorAdapter(new TypeUsageTransformerVisitor(
                $typeUsageTransformer
            ))
        );

        $traverser1->addVisitor(
            new PhpParserVisitorAdapter(new TypeDefinitionTransformerVisitor(
                $typeDefAssignments
            ))
        );

        $traverser1->addVisitor(
            new PhpParserVisitorAdapter(new PhpDocTransformerVisitor(
                new ReplaceTypePhpDocTransformer(
                    $typeUsageTransformer,
                    $this->phpParserDocToPhpdoc,
                    $this->phpDocSerializer
                )
            ))
        );


        $traverser2 = new NodeTraverser();

        $uses = array();

        foreach ($typeParameters as $typeParameter) {
            if (!$typeParameter->isNative()) {
                $uses[] = new Use_($typeParameter);
            }
        }

        $usesSimplifier = new Uses($uses);

        $traverser2->addVisitor(
            new PhpParserVisitorAdapter(new AddUsesVisitor($uses))
        );

        $traverser2->addVisitor(
            new PhpParserVisitorAdapter($this->namespaceContextVisitor)
        );

        $traverser2->addVisitor(
            new PhpParserVisitorAdapter(new TypeUsageTransformerVisitor(
                new SimplifierNameTransformer($usesSimplifier)
            ))
        );

        $traverser2->addVisitor(
            new PhpParserVisitorAdapter(new PhpDocTransformerVisitor(
                new ReplaceTypePhpDocTransformer(
                    new SimplifierNameTransformer($usesSimplifier),
                    $this->phpParserDocToPhpdoc,
                    $this->phpDocSerializer
                )
            ))
        );


        return new PhpParserSourceTransformer(
            $this->phpParser,
            $this->phpPrettyPrinter,
            array(
                $traverser1,
                $traverser2
            )
        );
    }
}