<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer\Subnode;

use PhpParser\Node;

/**
 * Class ConditionalSubnodeTransformer
 * @package NicMart\Generics\AST\Transformer\Subnode
 */
class ConditionalSubnodeTransformer implements SubnodeTransformer
{
    /**
     * @var SubnodeTransformerCondition[]
     */
    private $transformerConditions = [];
    /**
     * @var SubnodeTransformer
     */
    private $defaultTransformer;

    /**
     * ConditionalSubnodeTransformer constructor.
     * @param SubnodeTransformerCondition[] $transformerConditions
     * @param SubnodeTransformer $defaultTransformer
     */
    public function __construct(
        array $transformerConditions,
        SubnodeTransformer $defaultTransformer = null
    ) {
        foreach ($transformerConditions as $transformerCondition) {
            $this->addTransformerCondition($transformerCondition);
        }

        $this->defaultTransformer =
            $defaultTransformer ?: new CompleteSubnodeTransformer()
        ;
    }

    /**
     * @param Node $node
     * @param callable $f
     * @return mixed
     */
    public function map(Node $node, callable $f)
    {
        foreach ($this->transformerConditions as $transformerCondition) {
            if ($transformerCondition->accept($node)) {
                return $transformerCondition->transformer()->map(
                    $node,
                    $f
                );
            }
        }

        return $this->defaultTransformer->map($node, $f);
    }

    /**
     * @param SubnodeTransformerCondition $transformerCondition
     */
    private function addTransformerCondition(
        SubnodeTransformerCondition $transformerCondition
    ) {
        $this->transformerConditions[] = $transformerCondition;
    }
}