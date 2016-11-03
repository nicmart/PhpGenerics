<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Feature\Context;


use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use NicMart\Generics\AST\Context\NamespaceContextNodeExtractor;
use NicMart\Generics\AST\Transformer\BottomUpNodeTransformer;
use NicMart\Generics\AST\Transformer\ContextDependentNodeTransformer;
use NicMart\Generics\AST\Transformer\Name\NameAdapterPhpNameTransformer;
use NicMart\Generics\AST\Transformer\Name\NameManipulatorNodeTransformer;
use NicMart\Generics\AST\Transformer\TopDownNodeTransformer;
use NicMart\Generics\Infrastructure\PhpParser\Name\ChainNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\ClassNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\NameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\NameNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\UseUseNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Parser\AngleQuotedGenericTypeNameParser;
use NicMart\Generics\Name\Transformer\TypeNameTransformer;
use NicMart\Generics\Type\Parser\GenericTypeParserAndSerializer;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\Transformer\ByCallableTypeTransformer;
use NicMart\Generics\Type\Transformer\TypeTransformer;
use PhpParser\Node\Name;

/**
 * Class TypeTransformationContext
 * @package NicMart\Generics\Feature\Context
 */
class TypeTransformationContext implements Context
{
    /**
     * @var NodeTransformationContext
     */
    private $nodeContext;

    /**
     * @var TypeParser
     */
    private $typeParser;

    /**
     * @var TypeSerializer
     */
    private $typeSerializer;

    /**
     * @var PhpNameAdapter
     */
    private $phpNameAdapter;

    /**
     * @var callable
     */
    private $contextToTypeTransformation;

    /**
     * @var callable
     */
    private $contextToNameTransformation;

    /**
     * @var callable
     */
    private $contextToNodeTransformation;

    /**
     * @var NameManipulator
     */
    private $defaultNameManipulator;

    /**
     * TypeTransformationContext constructor.
     */
    public function __construct()
    {
        $this->typeParser = new GenericTypeParserAndSerializer(
            new AngleQuotedGenericTypeNameParser()
        );
        $this->typeSerializer = $this->typeParser;
        $this->phpNameAdapter = new PhpNameAdapter();
        $this->defaultNameManipulator = new ChainNameManipulator([
            new UseUseNameManipulator(),
            new ClassNameManipulator(),
            new NameNameManipulator()
        ]);
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->nodeContext = $environment->getContext(
            NodeTransformationContext::class
        );
    }

    /**
     * @Given the constant type transformation :type
     */
    public function theConstantTypeTransformation($type)
    {
        $this->contextToTypeTransformation =
            function (NamespaceContext $namespaceContext) use ($type) {
                return new ByCallableTypeTransformer(
                    function () use ($type, $namespaceContext) {
                        return $this->typeParser->parse(
                            FullName::fromString($type),
                            NamespaceContext::emptyContext()
                        );
                    }
                );
            }
        ;
    }

    /**
     * @When I build the name transformer from the type transformer
     */
    public function iBuildTheNameTransformerFromTheTypeTransformer()
    {

        $this->contextToNameTransformation = function (NamespaceContext $ns) {

            return new NameManipulatorNodeTransformer(
                $this->defaultNameManipulator,
                new NameAdapterPhpNameTransformer(
                    new TypeNameTransformer(
                        $ns,
                        $this->typeParser,
                        call_user_func($this->contextToTypeTransformation, $ns),
                        $this->typeSerializer
                    ),
                    new PhpNameAdapter()
                )
            );

        };
    }


    /**
     * @When I build the node transformer from the name transformer
     */
    public function iBuildTheNodeTransformerFromTheNameTransformer()
    {
        $this->contextToNodeTransformation = function (NamespaceContext $ns) {
            return new NameManipulatorNodeTransformer(
                $this->defaultNameManipulator,
                call_user_func($this->contextToNameTransformation, $ns)
            );
        };

        $this->nodeContext->iUseTheRawNodeTransformation(
            $this->nodeTransformation()
        );
    }

    /**
     * @Given the default name manipulator
     */
    public function theDefaultNameManipulator()
    {
        $this->nameManipulator = new ChainNameManipulator([
            new UseUseNameManipulator(),
            new ClassNameManipulator(),
            new NameNameManipulator()
        ]);
    }

    /**
     * @When I make the context dependent transformer :type-recursive
     */
    public function iMakeTheTransformerRecursive($recursionType)
    {
        $contextToNodeTransformation = $this->contextToNodeTransformation;
        $this->contextToNodeTransformation = function (NamespaceContext $ns) use (
            $contextToNodeTransformation, $recursionType
        ) {
            return $recursionType == "top-down"
                ? new TopDownNodeTransformer(
                    $this->nodeContext->subNodeTransformer(),
                    $contextToNodeTransformation($ns)
                )
                : new BottomUpNodeTransformer(
                    $this->nodeContext->subNodeTransformer(),
                    $contextToNodeTransformation($ns)
                )
            ;
        };

        $this->nodeContext->iUseTheRawNodeTransformation(
            $this->nodeTransformation()
        );
    }

    private function nodeTransformation()
    {
        return new ContextDependentNodeTransformer(
            new NamespaceContextNodeExtractor(),
            $this->contextToNodeTransformation
        );
    }
}