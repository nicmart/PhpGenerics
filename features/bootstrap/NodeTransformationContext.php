<?php
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use NicMart\Generics\AST\Transformer\BottomUpNodeTransformer;
use NicMart\Generics\AST\Transformer\NodeTransformer;
use NicMart\Generics\AST\Transformer\Subnode\ConditionalSubnodeTransformer;
use NicMart\Generics\AST\Transformer\Subnode\ExcludeSubnodesTransformer;
use NicMart\Generics\AST\Transformer\Subnode\SubnodeTransformer;
use NicMart\Generics\AST\Transformer\Subnode\SubnodeTransformerCondition;
use NicMart\Generics\AST\Transformer\TopDownNodeTransformer;
use PhpParser\ParserFactory;

/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */
class NodeTransformationContext implements Context
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
    protected $transformation;

    /**
     * @var SubnodeTransformer
     */
    private $subNodeTransformer;

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

        $this->subNodeTransformer = new ConditionalSubnodeTransformer(
            $this->subnodeMapperConditions
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
     * @When I make the transformer :type-recursive
     */
    public function iMakeTheTransformerRecursive2($recursionType)
    {
        $this->transformation = $recursionType == "top-down"
            ? new TopDownNodeTransformer(
                $this->subNodeTransformer,
                $this->transformation
            )
            : new BottomUpNodeTransformer(
                $this->subNodeTransformer,
                $this->transformation
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


    /**
     * @Then /^the code should be transformed to:$/
     */
    public function theCodeShouldBeTransformedTo(PyStringNode $string)
    {
        $expectedAST = $this->parser->parse("<?php\n" . $string);

        $this->assertSameAST($expectedAST, $this->transformedAST);
    }


    /**
     * @Given the raw node transformation:
     */
    public function theRawNodeTransformation(PyStringNode $pyStringNode)
    {
        $this->setNodeTransformer(eval($pyStringNode->getRaw()));
    }

    private function setNodeTransformer(NodeTransformer $transformer)
    {
        $this->transformation = $transformer;
    }


    /**
     * @param NodeTransformer $nodeTransformer
     * @return $this
     */
    public function iUseTheRawNodeTransformation(NodeTransformer $nodeTransformer)
    {
        $this->transformation = $nodeTransformer;

        return $this;
    }

    private function assertSameAST($nodes1, $nodes2)
    {
        PHPUnit_Framework_Assert::assertEquals(
            $this->serializer->prettyPrint($nodes1),
            $this->serializer->prettyPrint($nodes2)
        );
    }
}