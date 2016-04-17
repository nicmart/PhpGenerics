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
use PhpParser\Comment\Doc;

/**
 * Class SimplifyNamesPhpDocTransformer
 * @package NicMart\Generics\Compiler\PhpDoc
 */
class SimplifyNamesPhpDocTransformer implements PhpDocTransformer
{
    public function transform(
        Doc $docBlock,
        NamespaceContext $namespaceContext
    ) {
        // TODO: Implement transform() method.
    }
}