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
use NicMart\Generics\AST\Visitor\NamespaceContextVisitor;
use NicMart\Generics\Infrastructure\PhpParser\Transformer\DefaultNodeTransformer;
use NicMart\Generics\Infrastructure\Source\Transformer\PhpParserSourceTransformer;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Name\Transformer\NameQualifier;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\Lexer;
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
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * DefaultGenericTransformerProvider constructor.
     * @param Parser $phpParser
     * @param Standard $phpPrettyPrinter
     * @param PhpParserDocToPhpdoc $phpParserDocToPhpdoc
     * @param Serializer $phpDocSerializer
     * @param NamespaceContextVisitor $namespaceContextVisitor
     * @param GenericNameFactory $genericNameFactory
     */
    public function __construct(
        Parser $phpParser,
        Standard $phpPrettyPrinter,
        PhpParserDocToPhpdoc $phpParserDocToPhpdoc,
        Serializer $phpDocSerializer,
        NamespaceContextVisitor $namespaceContextVisitor,
        GenericNameFactory $genericNameFactory
    ) {
        $this->phpParser = $phpParser;
        $this->phpPrettyPrinter = $phpPrettyPrinter;
        $this->phpParserDocToPhpdoc = $phpParserDocToPhpdoc;
        $this->phpDocSerializer = $phpDocSerializer;
        $this->namespaceContextVisitor = $namespaceContextVisitor;
        $this->genericNameFactory = $genericNameFactory;
    }

    /**
     * @param GenericName $generic
     * @param FullName[] $typeParameters
     * @return PhpParserSourceTransformer
     */
    public function transformer(
        GenericName $generic,
        array $typeParameters
    ) {
        return new PhpParserSourceTransformer(
            $this->phpParser,
            new DefaultNodeTransformer(
                $this->phpParserDocToPhpdoc,
                $this->phpDocSerializer,
                $this->namespaceContextVisitor,
                $this->genericNameFactory,
                $generic,
                $typeParameters
            ),
            $this->phpPrettyPrinter
        );
    }
}