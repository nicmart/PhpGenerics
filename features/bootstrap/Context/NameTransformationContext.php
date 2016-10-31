<?php

namespace NicMart\Generics\Feature\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use NicMart\Generics\AST\Transformer\Name\NameManipulatorNodeTransformer;
use NicMart\Generics\Infrastructure\PhpParser\Name\ChainNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\ClassNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\NameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\NameNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\UseUseNameManipulator;
use PhpParser\Node;
use PhpParser\Node\Name;

/**
 * Defines application features from the specific context.
 */
class NameTransformationContext implements Context
{
    /**
     * @var callable
     */
    private $nameTransformation;

    /**
     * @var NameManipulator
     */
    private $nameManipulator;

    /**
     * @var NodeTransformationContext
     */
    private $nodeContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->nodeContext = $environment->getContext(NodeTransformationContext::class);
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
     * @Given the name transformation :from -> :to
     */
    public function theSingleTransformation($from, $to)
    {
        $this->theNameTransformation(new TableNode([
            ["from", "to"],
            [$from, $to]
        ]));
    }

    /**
     * @Given the constant name transformation :name
     */
    public function theConstantNameNodeTransformation($name)
    {
        $this->nameTransformation = function (Node $node) use ($name) {
            return $node instanceof Name
                ? new Name($name)
                : $node
            ;
        };
    }

    /**
     * @Given the name transformation that appends :suffix to names
     */
    public function theTransformationThatAppendsToNames($suffix)
    {
        $this->nameTransformation = function (Node $node) use ($suffix) {
            return $node instanceof Name
                ? new Name($node->toString() . $suffix)
                : $node
            ;
        };
    }

    /**
     * @Given /^the name transformation:$/
     */
    public function theNameTransformation(TableNode $table)
    {
        $names = [];
        foreach ($table as $row) {
            $names[] = [
                "from" => new Name\FullyQualified($row["from"]),
                "to" => new Name\FullyQualified($row["to"]),
            ];
        }

        $transformation = function (Name $name) use ($names) {
            foreach ($names as $namePair) {
                if ($name->parts == $namePair["from"]->parts) {
                    return $namePair["to"];
                }
            }

            return $name;
        };

        $this->nameTransformation = $transformation;
    }

    /**
     * @When I build the non-recursive node transformer from the name transformer
     */
    public function iBuildTheNonRecursiveNodeTransformerFromTheNameTransformer()
    {
        $this->nodeContext->iUseTheRawNodeTransformation(new NameManipulatorNodeTransformer(
            $this->nameManipulator,
            $this->nameTransformation
        ));
    }
}
