<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use NicMart\Generics\AST\Transformer\Name\NameNodeTransformer;
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
    public function theTransformation($from, $to)
    {
        $this->transformation = function (Name $name) use ($from, $to) {
            $fromName = new Name\FullyQualified($from);
            $toName = new Name\FullyQualified($to);

            if ($name->parts == $fromName->parts) {
                return $toName;
            }

            return $name;
        };
    }

    /**
     * @When /^I apply the foregoing$/
     */
    public function iApplyTheForegoing()
    {
        $transformer = new NameNodeTransformer($this->transformation);

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
}
