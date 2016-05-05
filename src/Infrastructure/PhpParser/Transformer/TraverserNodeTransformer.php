<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser\Transformer;

use NicMart\Generics\Adapter\PhpParserVisitorAdapter;
use NicMart\Generics\AST\Transformer\NodeTransformer;
use NicMart\Generics\AST\Visitor\Visitor;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;

/**
 * Class TraverserNodeTransformer
 * @package NicMart\Generics\Infrastructure\PhpParser\Transformer
 */
class TraverserNodeTransformer implements NodeTransformer
{
    /**
     * @var NodeTraverserInterface
     */
    private $traverser;

    /**
     * @param Visitor[] $visitors
     * @return TraverserNodeTransformer
     */
    public static function fromVisitors(array $visitors)
    {
        $traverser = new NodeTraverser();

        foreach ($visitors as $visitor) {
            $traverser->addVisitor(new PhpParserVisitorAdapter($visitor));
        }

        return new self($traverser);
    }

    /**
     * TraverserNodeTransformer constructor.
     * @param NodeTraverserInterface $traverser
     */
    public function __construct(NodeTraverserInterface $traverser)
    {
        $this->traverser = $traverser;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes)
    {
        $this->traverser->traverse($nodes);

        return $nodes;
    }
}