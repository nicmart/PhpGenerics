<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer;

use PhpParser\Node;

/**
 * Interface NodeTransformer
 * @package NicMart\Generics\Infrastructure\PhpParser\Transformer
 */
interface NodeTransformer
{
    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function transformNodes(array $nodes);

    /**
     * @param Node $node
     * @return Node
     */
    public function __invoke(Node $node);
}