<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Compiler\PhpDoc;


use NicMart\Generics\Name\Assignment\NamespaceAssignmentContext;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\NamespaceContext;
use phpDocumentor\Reflection\DocBlock;

interface PhpDocTransformer
{
    /**
     * @param DocBlock $docBlock
     * @param NamespaceContext $namespaceContext
     * @param NameAssignmentContext $nameAssignmentContext
     * @return DocBlock
     */
    public function transform(
        DocBlock $docBlock,
        NamespaceContext $namespaceContext,
        NameAssignmentContext $nameAssignmentContext
    );
}