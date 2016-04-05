<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Compiler;

use NicMart\Generics\Name\Assignment\TypeAssignmentContext;
use NicMart\Generics\Name\Context\NamespaceContext;
use PhpParser\Node;

/**
 * Interface Compiler
 * @package NicMart\Generics\Compiler
 */
interface Compiler
{
    /**
     * @param Node $node
     * @param NamespaceContext $namespaceContext
     * @param TypeAssignmentContext $typeAssignmentContext
     * @return Node
     */
    public function compile(
        Node $node,
        NamespaceContext $namespaceContext,
        TypeAssignmentContext $typeAssignmentContext
    );
}