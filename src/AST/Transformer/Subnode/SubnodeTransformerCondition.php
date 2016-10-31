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
 * Class AcceptingSubnodeTransformer
 * @package NicMart\Generics\AST\Transformer\Subnode
 */
final class SubnodeTransformerCondition
{
    /**
     * @var SubnodeTransformer
     */
    private $transformer;

    /**
     * @var
     */
    private $class;

    /**
     * AcceptingSubnodeTransformer constructor.
     * @param SubnodeTransformer $transformer
     * @param $class
     */
    public function __construct(SubnodeTransformer $transformer, $class)
    {
        $this->transformer = $transformer;
        $this->class = $class;
    }

    /**
     * @return SubnodeTransformer
     */
    public function transformer()
    {
        return $this->transformer;
    }

    /**
     * @param Node $node
     * @return bool
     */
    public function accept(Node $node)
    {
        return $node instanceof $this->class;
    }
}