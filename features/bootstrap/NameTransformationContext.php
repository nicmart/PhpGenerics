<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use NicMart\Generics\AST\Transformer\BottomUpNodeTransformer;
use NicMart\Generics\AST\Transformer\ByCallableNodeTransformer;
use NicMart\Generics\AST\Transformer\Name\NameManipulatorNodeTransformer;
use NicMart\Generics\AST\Transformer\Name\NameNodeTransformerBuilder;
use NicMart\Generics\AST\Transformer\NodeFunctor;
use NicMart\Generics\AST\Transformer\NodeTransformer;
use NicMart\Generics\AST\Transformer\Subnode\ConditionalSubnodeTransformer;
use NicMart\Generics\AST\Transformer\Subnode\ExcludeSubnodesTransformer;
use NicMart\Generics\AST\Transformer\Subnode\SubnodeTransformerCondition;
use NicMart\Generics\AST\Transformer\TopDownNodeTransformer;
use NicMart\Generics\Infrastructure\PhpParser\Name\ChainNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\ClassNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\NameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\NameNameManipulator;
use NicMart\Generics\Infrastructure\PhpParser\Name\UseUseNameManipulator;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\ParserFactory;

/**
 * Defines application features from the specific context.
 */
class NameTransformationContext implements Context
{
    /**
     * @var \PhpParser\Parser
     */
    private $parser;

    /**
     * @var \PhpParser\PrettyPrinter\Standard
     */
    private $serializer;

    /**
     * @var \PhpParser\Node[]
     */
    private $codeAST;

    /**
     * @var \PhpParser\Node[]
     */
    private $transformedAST;

    /**
     * @var NodeTransformer
     */
    private $transformation;

    /**
     * @var callable
     */
    private $nameTransformation;

    /**
     * @var NameManipulator
     */
    private $nameManipulator;

    /**
     * @var string
     */
    private $recursionType;

    /**
     * NodeClass => SET of Subnode names
     * @var SubnodeTransformerCondition[]
     */
    private $subnodeMapperConditions = [];

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
        $this->serializer = new PhpParser\PrettyPrinter\Standard();
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
     * @Given we recurse :recursionType
     */
    public function weRecurse($recursionType)
    {
        $this->recursionType = $recursionType;
    }

    /**
     * @Given /^nodes of type \'([^\']*)\' do not map on subnodes \'([^\']*)\'$/
     */
    public function nodesOfTypeDoNotMapOnSubnodes($nodeType, $subnodesNamesCsv)
    {
        $nodeClass = '\\PhpParser\\Node\\' . $nodeType;
        $subnodeNames = explode(",", $subnodesNamesCsv);

        $this->subnodeMapperConditions[] = new SubnodeTransformerCondition(
            new ExcludeSubnodesTransformer($subnodeNames),
            $nodeClass
        );
    }

    /**
     * @Given the code:
     */
    public function theCode(PyStringNode $string)
    {
        $this->codeAST = $this->parser->parse("<?php\n\n" . $string->getRaw());
    }


    /**
     * @Given the transformation :from -> :to
     */
    public function theSingleTransformation($from, $to)
    {
        $this->theTransformation(new TableNode([
            ["from", "to"],
            [$from, $to]
        ]));
    }

    /**
     * @Given the constant name node transformation :name
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
     * @Given /^the transformation:$/
     */
    public function theTransformation(TableNode $table)
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
     * @When I build the node transformation
     */
    public function iBuildTheNodeTransformation()
    {
        $nonRecursiveTransformation = new NameManipulatorNodeTransformer(
            $this->nameManipulator,
            $this->nameTransformation
        );

        $subNodeTransformer = new ConditionalSubnodeTransformer(
            $this->subnodeMapperConditions
        );

        $this->transformation = $this->recursionType == "top-down"
            ? new TopDownNodeTransformer(
                $subNodeTransformer,
                $nonRecursiveTransformation
            )
            : new BottomUpNodeTransformer(
                $subNodeTransformer,
                $nonRecursiveTransformation
            )
        ;
    }

    /**
     * @When I apply it to the code
     */
    public function iApplyItToTheCode()
    {
        $this->transformedAST = $this
            ->transformation
            ->transformNodes($this->codeAST)
        ;
    }

    /**
     * @Then the code should remain unchanged
     */
    public function theCodeShouldRemainUnchanged()
    {
        $this->assertSameAST($this->codeAST, $this->transformedAST);
    }

    private function assertSameAST($nodes1, $nodes2)
    {
        PHPUnit_Framework_Assert::assertEquals(
            $this->serializer->prettyPrint($nodes1),
            $this->serializer->prettyPrint($nodes2)
        );
    }

    /**
     * @Then /^the code should be transformed to:$/
     */
    public function theCodeShouldBeTransformedTo(PyStringNode $string)
    {
        $expectedAST = $this->parser->parse("<?php\n" . $string);

        $this->assertSameAST($expectedAST, $this->transformedAST);
    }
}
