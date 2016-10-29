<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use NicMart\Generics\AST\Transformer\Name\NameNodeTransformerBuilder;
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
     * @var callable
     */
    private $transformation;

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

        $this->transformation = function (Name $name) use ($names) {
            foreach ($names as $namePair) {
                if ($name->parts == $namePair["from"]->parts) {
                    return $namePair["to"];
                }
            }

            return $name;
        };
    }

    /**
     * @When /^I apply the foregoing$/
     */
    public function iApplyTheForegoing()
    {
        $transformer = NameNodeTransformerBuilder::build($this->transformation);

        $this->transformedAST = $transformer->transformNodes($this->codeAST);
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
