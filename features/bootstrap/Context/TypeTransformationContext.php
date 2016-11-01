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
     * @var NameTransformationContext
     */
    private $nameContext;

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
     * @var TypeTransformer
     */
    private $typeTransformation;

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
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->nameContext = $environment->getContext(
            NameTransformationContext::class
        );
    }

    /**
     * @Given the constant type transformation :type
     */
    public function theConstantTypeTransformation($type)
    {
        $type = $this->typeParser->parse(
            FullName::fromString($type),
            NamespaceContext::emptyContext()
        );

        $this->typeTransformation = new ByCallableTypeTransformer(
            function () use ($type) {
                return $type;
            }
        );
    }

    /**
     * @When I build the name transformer from the type transformer
     */
    public function iBuildTheNameTransformerFromTheTypeTransformer()
    {
        $this->nameContext->theRawNameTransformation(
            function (Name $phpName) {
                $nameTransformer = new TypeNameTransformer(
                    NamespaceContext::emptyContext(),
                    $this->typeParser,
                    $this->typeTransformation,
                    $this->typeSerializer
                );

                $fromName = $this->phpNameAdapter->fromPhpName($phpName);

                return $this->phpNameAdapter->toPhpName(
                    $nameTransformer($fromName)
                );
            }
        );
    }
}