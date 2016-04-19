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

use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;

/**
 * Class SimplifyNamesPhpDocTransformer
 * @package NicMart\Generics\Compiler\PhpDoc
 */
class SimplifyNamesPhpDocTransformer extends AbstractPhpDocTransformer
{
    /**
     * @param string $type
     * @param NamespaceContext $namespaceContext
     * @return string
     */
    protected function transformType(
        $type,
        NamespaceContext $namespaceContext
    ) {
        $from = FullName::fromString($type);

        return $namespaceContext->simplify($from)->toString();
    }
}