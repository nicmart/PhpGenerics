<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\PhpDoc;

use NicMart\Generics\Name\Context\NamespaceContext;
use phpDocumentor\Reflection\DocBlock;
use PhpParser\Comment\Doc;

/**
 * Interface PhpDocTransformer
 * @package NicMart\Generics\Compiler\PhpDoc
 */
interface PhpDocTransformer
{
    /**
     * @param Doc $docBlock
     * @param NamespaceContext $namespaceContext
     * @return Doc
     */
    public function transform(
        Doc $docBlock,
        NamespaceContext $namespaceContext
    );
}